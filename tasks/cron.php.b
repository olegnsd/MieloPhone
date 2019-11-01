#!/usr/bin/php
<?php
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);


    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."sms.php";
    include $_SESSION['CONF']['DIRS']['LIB']."pattern.php";
    include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";
    include $_SESSION['CONF']['DIRS']['LIB']."class.phpmailer.php";

    function sendMailActive($email, $name, $mark, $prev, $current) {
        $mail = new PHPMailer(true);

#        $mail->IsMail();
#        $mail->SMTPDebug  = 0;
#        $mail->SMTPAuth   = true;
#        $mail->SMTPSecure = 'ssl';
#        $mail->Host       = "smtp.gmail.com";
#        $mail->Port       = 465;
#        $mail->Username   = "ki0001026@gmail.com";
#        $mail->Password   = 'abc999cba';
        $mail->CharSet    = "UTF-8";

            if ($prev == "yes" && $current == "no") {
                $message = "{$name} ({$mark}) offline";
            }
            else if ($prev == "no" && $current == "yes"){
                $message = "{$name} ({$mark}) online";
            }

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



        function sendSmsGoIpState($prev, $current, $phone, $name, $mark) {
	    $ok = false;

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

	    return $ok;
        }


   $db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
#    $db->sql_query("use ring;");
#    $db->sql_query("update tasks set state='new' where id= 52;");
#    $db->sql_query("update tasks_base set send = 'N', total = 0, state=NULL where tid=52;");

    $task = new controller();

    $sounds = $task->getCallerList();

    $current = $task->getCurrentTask();

#    if (count($current) >= 5) // Если 3 и более задач
#	exit;

    $process = $task->getTaskForProcessing($current);
    if (!(!empty($process) && count($process) > 0))
	exit;

    $callers = explode(",", $process['caller']);
    $caller = 0;

    if (file_exists($_SESSION['CONF']['DIRS']['SOUNDS'].$process['id'].".wav"))
	foreach ($callers as $c) 
	    copy($_SESSION['CONF']['DIRS']['SOUNDS'].$process['id'].".wav", $_SESSION['CONF']['DIRS']['ASTSOUNDS'].$sounds[$c].".wav");

    $template = file_get_contents(__DIR__."/template.tpl");

    function createRing($contact, $caller, $template) {
#	$send = str_replace("{!PHONE!}", $contact['phone'], str_replace("{!CALLER!}", $caller, $template));
	$send = str_replace("{!PHONE!}", (strlen($contact['phone']) == 10? "+7".$contact['phone']: $contact['phone']), str_replace("{!CALLER!}", $caller, $template));

	file_put_contents($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], $send);
#	echo $_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id']."\n";
        chmod($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], 0777);
#	chown ($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], "asterisk");
#        chgrp ($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], "asterisk");
    }
    echo "---------------\n";
    $task->updateTaskState($process['id'], "current");


    while (true) {

	$base = $task->getBase($process['id']);
        $count = 0;
#        print_r($process);
#        print_r($base);
#        exit;
	if (!empty($base) && count($base) > 0) {
	    foreach ($base as $contact) {
		if ($task->isBlackPhone($contact['phone'], $process['client_id'])) {
		    $task->updateBaseSend($contact['id'], 'F');
		    $task->updateTaskSend($process['id'], 1);
		    continue;
		}

		$date = date("Y-m-d H:i:s");
#		echo "---------------\n";
		createRing($contact, $callers[$caller], $template);
#exit;
		$count++;
		$caller++;
		if ($caller >= count($callers))
		    $caller = 0;

		$task->updateBaseSend($contact['id'], 'Y');
	        $task->updateTaskSend($process['id'], 1);

		$start = time();

		// проверяем статус звонка, если дозвон прошел, то идем к следующему звонку
		while (time() - $start < $process['sleep']) {
			$getRingState = $task->getAskeriskRingState($contact['phone'], $date);

			if ($getRingState['disposition'] == "ANSWERED") {
				break;
			}
			sleep(2);
		}

		$fp = fopen("/home/easyring/web/easyring24.com/tasks/db.log", "a");
		fwrite($fp, date("H:i:s\t").$process['id']."\t".$contact['phone']."\t".json_encode($getRingState)."\n");
		fclose($fp);

		$task->updateRingState($contact, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: "NO ANSWER"));

		$state = $task->editCron($process['id']);
		if ($state['state'] != "current")
			exit;

		if (str_replace(":", "", $state['timeto']) <= date("Hi", strtotime("NOW {$state['timezone']} HOUR"))) {
			while (true) {
			    if (str_replace(":", "", $state['timefrom']) <= date("Hi", strtotime("NOW {$state['timezone']} HOUR")) &&
				str_replace(":", "", $state['timeto']) >= date("Hi", strtotime("NOW {$state['timezone']} HOUR"))			    
			    )
				break;
			    sleep(60);
			}
		}

		if (!$task->isCallerActive($process['caller'])) { // Если коллер не активен
			$task->updateTaskState($process['id'], "pause");

                        $userphone = $db->sql_query("select * from users where id = {$process['uid']}")->resultRow();

			if (800 <= (int) date("Hi", strtotime("NOW {$userphone['timezone']} HOUR")) &&
			    2359 >= (int) date("Hi", strtotime("NOW {$userphone['timezone']} HOUR"))			    
			) {
			    $callerInfo  = $task->callerInfo($process['caller']);

			    if ($callerInfo['send'] == 1) {
                    		if ($userphone['phone'] != "") {
                        	    $ok = sendSmsGoIpState("yes", "no", $userphone['phone'], $callerInfo['name'], $callerInfo['mark']);
				}
			    }
                            if ($callerInfo['send'] == 2 && $userphone['email'] != "") {
                                sendMailActive($userphone['email'], $callerInfo['name'], $callerInfo['mark'], $callerInfo['active'], $active);
                            }


			}

			exit;
		}

		//sleep($process['sleep']);
	    }
	}
	else {
	    break;
	}
    }

#exit;

/*
    sleep(120);

    $ids = array();
    $getNoStateTaskBase = $task->getNoStateTaskBase($process['id']);
    if (!empty($getNoStateTaskBase) && count($getNoStateTaskBase) > 0) {
	foreach ($getNoStateTaskBase as $person) {
	    $getRingState = $task->getAskeriskRingState($person['phone'], $person['datering']);

	    if ($getRingState['disposition'] != "ANSWERED") {
		$ids[] = $person['id'];
	    }
	    else {
		$task->updateRingState($person, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: ""));
	    }
	}
    }

    if (!empty($ids) && count($ids) > 0) {
	while (count($ids) > 0) {
	    foreach ($ids as $key=>$id) {
		$caller++;
		if ($caller >= count($callers))
		    $caller = 0;

		$info = $task->getRingInfo($id);
		$getRingState = $task->getAskeriskRingState($info['phone'], $info['datering']);

		if ($info['total'] >= 5) { // если больше 5 звонков, то больше не звоним
		    $task->updateRingState($info, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: ""));
		    unset($ids[$key]); 
		}
		else {
		    if ($getRingState['disposition'] != "ANSWERED") {
			createRing($info, $callers[$caller], $template);
			$task->updateRingState($info, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: ""));
		    }
		    else {
			$task->updateRingState($info, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: ""));
			unset($ids[$key]);
		    }


		    $start = time();

		    // проверяем статус звонка, если дозвон прошел, то идем к следующему звонку
		    while (time() - $start < $process['sleep']) {
			$getRingState = $task->getAskeriskRingState($info['phone'], $date);

			if ($getRingState['disposition'] == "ANSWERED") {
				$task->updateRingState($contact, (!empty($getRingState) && count($getRingState)>0? $getRingState['disposition']: ""));
				break;
			}
			sleep(5);
		    }

		    if (!$task->isCallerActive($process['caller'])) { // Если коллер не активен
			$task->updateTaskState($process['id'], "pause");
			exit;
		    }


//		    sleep(60);
		}
		
		$state = $task->editCron($process['id']);
		if ($state['state'] != "current")
			exit;
		if (str_replace(":", "", $state['timeto']) <= date("Hi", strtotime("NOW {$state['timezone']} HOUR"))) {
			while (true) {
			    if (str_replace(":", "", $state['timefrom']) <= date("Hi", strtotime("NOW {$state['timezone']} HOUR")) &&
				str_replace(":", "", $state['timeto']) >= date("Hi", strtotime("NOW {$state['timezone']} HOUR"))			    
			    )
				break;
			    sleep(60);
			}
		}


	    }
	}
    }

    sleep(120); // ждем две минуты, чтобы дошли все звонки
    */

		$fp = fopen("/home/easyring/web/easyring24.com/tasks/db.log", "a");
		fwrite($fp, "отправка email\n");
		fclose($fp);

    
    $task->updateTaskState($process['id'], "send");
//    include $_SESSION['CONF']['DIRS']['LIB']."class.phpmailer.php";

    $from_mail = "norelpy@easywork24.com";
    $from_name = "Конструктор Медиа";
    $subject = "Конструктор Медиа";

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPDebug  = false;
        $mail->SMTPAuth   = false;
        $mail->SMTPSecure = 'tls';
        $mail->Host       = "195.211.103.226";
        $mail->Port       = 25;
        $mail->Username   = $from_mail;
        $mail->Password   = 'xyz1516171919';
        $mail->CharSet    = "UTF-8";
$mail->smtpConnect(                                                                                                                                                          
    array(                                                                                                                                                                   
        "ssl" => array(                                                                                                                                                      
            "verify_peer" => false,                                                                                                                                          
            "verify_peer_name" => false,                                                                                                                                     
            "allow_self_signed" => true                                                                                                                                      
        )                                                                                                                                                                    
    )                                                                                                                                                                        
);                


	$listResponseInterest = $task->getListResponseOnlyInterest($process['id']);
	$listResponse = $task->getListResponse($process['id']);
 
	$answered = 0;

	foreach ($listResponse as $r)
		if ($r['state'] == 'ANSWERED')
			$answered++;

	$text = "Всего абонентов: ".count($listResponse)."<BR/>".
		"Всего абонентов подняло трубку: {$answered}<br/>".
		"Всего абонентов откликнулось: ".count($listResponseInterest)."<BR><BR>";

	if (count($listResponseInterest) > 0) {
		$text .= "Абоненты, которые откликнулись:<br>";
		foreach ($listResponseInterest as $r)
			$text .= $r['phone'].($r['email'] != ""? ", email: {$r['email']}": "").($r['name'] != ""? ", имя: {$r['name']}": "");
	}

        try {
                foreach (explode(";", $process['email_notify']) as $e)
                    $mail->AddAddress($e);
//                    $mail->AddAddress("sergej-123@yandex.ru");
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = "Отчет о проделанной работе";
                $mail->MsgHTML($text);
                $mail->Send();
        } catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }

?>
