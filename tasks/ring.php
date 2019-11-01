#!/usr/bin/php
<?php 
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);
    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');
    require_once $_SESSION['CONF']['DIRS']['LIB']."mysql.php";                                                                                                               
    require_once $_SESSION['CONF']['DIRS']['LIB']."sms.php";                                                                                                                 
    require_once $_SESSION['CONF']['DIRS']['LIB']."pattern.php";                                                                                                             
    require_once $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";                                                                                                         
    require_once $_SESSION['CONF']['DIRS']['LIB']."class.phpmailer.php";                                                                                                     
                                                                              
    $task = new controller();
    $process = $task->getCurrentTask();
    $fp = fopen("/home/easyring/web/easyring24.com/tasks/db.log", "a");
    if (!(!empty($process) && count($process)>0)) { fwrite($fp,"no active tasks\n");fclose($fp);  die ("no active tasks\n");}
     $argv[1] = str_replace("'", "", $argv[1]);
    if (strlen($argv[1]) == 12) $argv[1] = substr($argv[1], 2);
	fwrite($fp,"phone $argv[1] is up1\n");

    $from_mail = "noreply@mielophone.pw";
    $from_name = "Конструктор Медиа";
    $subject = "Конструктор Медиа";
    $ids = array();
    foreach ($process as $p)
	$ids[] = $p['id'];
    $process = $task->pressButtonContact($ids, $argv[1]);
    $contact = $task->getContact($process['id'], $argv[1]);

    if ($process['sms_enable'] == 1) {
        $sms = new Transport();
	 fwrite($fp, date("d-m H:i:s\t")."!!!! Отправка sms ".json_encode($process)."\n");
        $ok = $sms->send(array("text" => $process['sms_text']), array(strlen($argv[1]) == 10? $argv[1]: substr($argv[1], 4)));
    }
	else { fwrite($fp,"phone $argv[1] sms is not set to send\n");}
	

    if ($process['email_enable'] == 1) {//    if ($process['email_enable'] == 1) 
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host       = "46.150.101.251";
        $mail->Port       = "25";
        $mail->Username   = "noreply@mielophone.pw";
        $mail->Password   = 'TdsQR9F4NV';
        $mail->CharSet    = "UTF-8";
	$mail->smtpConnect(array("ssl" => array ("verify_peer" => false,"verify_peer_name" => false,"allow_self_signed" => true)));                                                  

        try {   $mail->AddAddress($contact['email']);
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = $subject;
                $mail->MsgHTML($process['email_text']);
                $mail->Send();} 
	catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }
 
                fwrite($fp, date("H:i:s\t")."!!!! Отправка email ".json_encode($process)."\n");

    }//    if ($process['email_enable'] == 1)                                                                                                                                     
		else { fwrite($fp,"phone $argv[1] mail is not set to send\n");}
if ($process['url_notify'] != "") //if ($process['email_notify'] != "")
{		$query = http_build_query(array('name' => $contact['name'],'email' => $contact['email'],'phone' => $contact['phone'],'date'  => $contact['datering']));
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $process['url_notify']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $output = curl_exec($ch);
                curl_close($ch);
                fwrite($fp, date("d:m H:i:s\t")."!!!! Дёргаем курл \n".json_encode($process)."\n");
                if ($output == "Call not found")  { fwrite($fp,"phone $argv[1] response from  url is Call not found \n"); 	fclose($fp); return;}
}	else { fwrite($fp,"phone $argv[1] url is not set to send\n");}
	
	fclose($fp);
    if ($process['email_notify'] != "") { //if ($process['email_notify'] != "")
	if ($process['client_id'] > 0) { $clientsTasks = $task->getTasksByClient($process['client_id']);
		$clientsTasks = array_map(function($task) {
	        	return $task['id'];
		}, $clientsTasks);
	}
	else {
		$clientsTasks = array($process['id']);
	}


        $mail = new PHPMailer(true);
        $mail->IsMail();
        $mail->SMTPDebug  = false;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = "46.150.101.251";
        $mail->Port       = 465;
        $mail->Username   = 'noreply@mielophone.pw';
        $mail->Password   = 'TdsQR9F4NV';
        $mail->CharSet    = "UTF-8";
        $text = "Телефон: ".$contact['phone']."<BR>".
    		"Дата и время отклика: ".date("d.m.Y H:i:s", strtotime($contact['datering']))."<BR>".
    		($process['sms_enable'] != ""? "Текст SMS: ".$process['sms_text']."<BR>": "").
    		"Звук: <a href='http://s.easyring24.com/download/sound/".md5(md5($process['id']))."'>http://s.easyring24.com/download/sound/".md5(md5($process['id']))."</a>";
        $listResponseInterest = $task->getListResponseOnlyInterest($clientsTasks, 0, 100000);
        list($listResponse, $answered) = $task->getListResponseStat($clientsTasks);
        $text .= "<br><br/><hr/>".
                "Всего абонентов подняло трубку: {$answered}<br/>".
                "Всего абонентов откликнулось: ".count($listResponseInterest)."<BR><BR>";
        if (count($listResponseInterest) > 0) {
                $text .= "Абоненты, которые откликнулись:<br>";
                foreach ($listResponseInterest as $r)
                        $text .= date("d.m.Y H:i:s", strtotime($r['datering'])).", телефон: ".(strpos($r['phone'], "+") !== false? substr($r['phone'], strpos($r['phone'], "+")): $r['phone']).($r['email'] != ""? ", email: {$r['email']}": "").($r['name'] != ""? ", имя: {$r['name']}": "")."<br/>";
        }        try {
		foreach (explode(";", $process['email_notify']) as $email)
	                $mail->AddAddress($email);
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = "Новый отклик ".($process['client_id'] > 0? " - ".$task->getClientName($process['client_id']): "");
                $mail->MsgHTML($text);
                $mail->Send();
        } catch (phpmailerException $e) {echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {echo $e->getMessage(); //Boring error messages from anything else!        
} } //end if ($process['email_notify'] != "")

        

?>
