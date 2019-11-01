#!/usr/bin/php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);

    mb_internal_encoding("UTF-8");
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."sms.php";
    include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";
    include $_SESSION['CONF']['DIRS']['LIB']."class.phpmailer.php";

    function sendMailActive($email, $name, $mark, $prev, $current) {
	    if ($prev == "yes" && $current == "no") {
	        $message = "{$name} ({$mark}) offline";
	    }
	    else if ($prev == "no" && $current == "yes"){
	        $message = "{$name} ({$mark}) online";
	    }

echo "$email, $name, $mark, $prev, $current\n";
echo $prev." - ".$current."\n\n";
#exit;

        $mail = new PHPMailer(true);

#        $mail->IsMail();
#        $mail->SMTPDebug  = 0;
#        $mail->SMTPAuth   = false;
#        $mail->SMTPSecure = 'ssl';
#        $mail->Host       = "smtp.gmail.com";
#        $mail->Port       = 465;
#        $mail->Username   = "ki0001026@gmail.com";
#        $mail->Password   = 'abc999cba';
        $mail->CharSet    = "UTF-8";

        try {
                $mail->AddAddress($email);
                $mail->SetFrom("ki0001026@gmail.com", "Конструктор Империй");
                $mail->Subject = "Конструктор Империй";
                $mail->MsgHTML($message);
                $mail->Send();
        } catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }

    }

    $task = new controller();

    $db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);

	function send($url, $post = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		if ($post !== false) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/asterisk/web/ring.vendors.name/tasks/cookiefile"); 
		curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/asterisk/web/ring.vendors.name/tasks/cookiefile"); 

		$result = curl_exec($ch);
	
		curl_close($ch);

		return $result;
	}

	function sendSmsGoIpState($prev, $current, $phone, $name, $mark) {
	    if ($prev == "yes" && $current == "no") {
	        $sms = new Transport();
	        $message = "{$name} ({$mark}) offline";
	        $ok = $sms->send(array("text" => $message), array($phone));
	        unset($sms);
	    }
	    else if ($prev == "no" && $current == "yes"){
	        $sms = new Transport();
	        $message = "{$name} ({$mark}) online";
	        $ok = $sms->send(array("text" => $message), array($phone));
	        unset($sms);
	    }
	}

    	$rowset = $db->sql_query("select * from callers where ussd != ''")->resultArray();

	if (!empty($rowset) && count($rowset) > 0) {
		$url 	= "http://goip.vendors.name/en/dologin.php";
		$post = array (
			"username"	=> "root",
			"password"	=> "dsaf45645nJKmgf",
			"Submit"	=> "Sign in",
			"lan"		=> 3
		);
		$ok = send($url, $post);

		foreach ($rowset as $row) {
#		print_r($row);
			$url	= "http://goip.vendors.name/en/ussd.php?debug=1&TERMID={$row['mark']}";
echo $url."\n";
			$post = array (
				"USSDMSG"	=> $row['ussd'],
				"Id"		=> "{\$rs['id']}",
				"Submit"	=> "Send"
			);

			$result = send($url, $post);
			preg_match_all("/<td height=\"22\"  class=\"tdbg\">(.*)<\/td>/", $result, $out);

#echo $result;
			$balance = (float) $out[1][0] + 0;
#			print_r($out);
echo $result;
echo $row['id']." - ".$balance."\n";
echo "\n\n\n\n\n\n";
#print_r($balance);

			$active = ($out[1][0] == ""? "no": ($balance >= 0? "yes": "no"));

                        if (800 <= (int) date("Hi", strtotime("NOW {$row['timezone']} HOUR")) &&
                            2359 >= (int) date("Hi", strtotime("NOW {$row['timezone']} HOUR")) &&
			    $row['active'] != $active
                        ) {
			    $task->addActiveState($row['id'], ($active == "no"? 0: 1));

			    if ($row['send'] == 1 && $row['phone'] != "") {
				sendSmsGoIpState($row['active'], $active, $row['phone'], $row['name'], $row['mark']);
			    }
			    if ($row['send'] == 2 && $row['email'] != "") {
				sendMailActive($row['email'], $row['name'], $row['mark'], $row['active'], $active);
			    }
			}

			$process = $task->getCurrentTask();
			if (!empty($process) && count($process) > 0) {
			    foreach ($process as $p) {
				if (in_array($row['mark'], explode(",", $p['caller']))) {
	                            if (800 <= (int) date("Hi", strtotime("NOW {$row['timezone']} HOUR")) &&
        		                2359 >= (int) date("Hi", strtotime("NOW {$row['timezone']} HOUR")) &&
					$row['active'] != $active
                    		    ) {
					$callerInfo  = $task->callerInfo($p['caller']);
					$task->addActiveState($callerInfo['id'], ($active == "no"? 0: 1));

    					$userphone = $db->sql_query("select * from users where id = {$p['uid']}")->resultRow();
					if ($callerInfo['send'] == 1) {
			    		    if ($userphone['phone'] != "") {
						sendSmsGoIpState($row['active'], $active, $userphone['phone'], $row['name'], $row['mark']);
					    }
					}
					if ($callerInfo['send'] == 2) {
					    if ($userphone['email'] != "")
						sendMailActive($userphone['email'], $callerInfo['name'], $callerInfo['mark'], $callerInfo['active'], $active);
					}
				    }
				}
			    }
			}

echo "update callers set active = '{$active}', balance = {$balance}, updateactive='".date("Y-m-d H:i:s")."' where id = {$row['id']}\n";

			$db->query("update callers set active = '{$active}', balance = {$balance}, updateactive='".date("Y-m-d H:i:s")."' where id = {$row['id']}");
			$db->query("insert into callers_active (cid, active, balance) VALUES ({$row['id']}, '".strtoupper(substr($active, 0, 1))."', {$balance})");
		}
	}


    	$rowset = $db->sql_query("select * from callers where ussd = '' or isnull(ussd)")->resultArray();

	if (!empty($rowset) && count($rowset) > 0) {
		unset($out);

                $command = "/usr/local/bin/show_peers.sh";
                exec ($command, $out);

                $callers = array();
                for ($i = 1; $i < count($out)-1; $i++) {
                        $name = substr($out[$i], 0, 25);
                        if (strpos($name, "/") !== false)
                                $name = substr($name, 0, strpos($name, "/"));

                        $state = substr($out[$i], 105, 10);
                        if (strpos($state, " ") !== false)
                                $state = substr($state, 0, strpos($state, " "));

                        $callers[trim($name)] = trim($state);
                }

print_r($callers);

		foreach ($rowset as $row) {
echo $row['mark']."\n";
			$active = ($callers[$row['mark']] == "OK"? "yes": "no");
#			if ($row['send'] == 1 && $row['phone'] != "")
#			    sendSmsGoIpState($row['active'], $active, $row['phone'], $row['name'], $row['mark']);
#			if ($row['send'] == 2 && $row['email'] != "")
#			    sendMailActive($row['email'], $row['name'], $row['mark'], $row['active'], $active);
			$process = $task->getCurrentTask();
			if (!empty($process) && count($process) > 0) {
			    foreach ($process as $p) {
				if (in_array($row['mark'], explode(",", $p['caller'])) && $row['active'] != $active) {
				    $task->addActiveState($row['id'], ($active == "no"? 0: 1));

				    $userphone = $db->sql_query("select * from users where id = {$p['uid']}")->resultRow();
				    if ($row['send'] == 1 && $userphone['phone'] != "")
					sendSmsGoIpState($row['active'], $active, $userphone['phone'], $row['name'], $row['mark']);
			    	    if ($row['send'] == 2 && $userphone['email'] != "")
					sendMailActive($userphone['email'], $row['name'], $row['mark'], $row['active'], $active);
				}
			    }
			}

			$db->query("update callers set active = '{$active}', balance = NULL, updateactive='".date("Y-m-d H:i:s")."' where id = {$row['id']}");
			$db->query("insert into callers_active (cid, active, balance) VALUES ({$row['id']}, '".strtoupper(substr($active, 0, 1))."', 0)");

		}
	}

?>