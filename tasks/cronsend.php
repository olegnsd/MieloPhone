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

   $db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
    
$task = new controller();
$temp = $task->editCron(1644);
$clientsTasks = $task->getTasksByClient($temp['client_id']);
$clientsTasks = array_map(function($task) {
	return $task['id'];
}, $clientsTasks);

    $from_mail = "norelpy@easywork24.com";
    $from_name = "Конструктор Империй";
    $subject = "Конструктор Империй";

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPDebug  = false;
#        $mail->SMTPAuth   = false;
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

	$listResponseInterest = $task->getListResponseOnlyInterest($clientsTasks, 0, 100000);

	$listResponse = $task->getListResponse($clientsTasks, 0, 100000);

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
			$text .= date("d.m.Y H:i:s", strtotime($r['datering'])).", телефон: ".(strpos($r['phone'], "+") !== false? substr($r['phone'], strpos($r['phone'], "+")): $r['phone']).($r['email'] != ""? ", email: {$r['email']}": "").($r['name'] != ""? ", имя: {$r['name']}": "")."<br/>";
	}

        try {
#                foreach (explode(";", $process['email_notify']) as $e)
#                    $mail->AddAddress($e);
                    $mail->AddAddress("psv@alfadevs.ru");
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
