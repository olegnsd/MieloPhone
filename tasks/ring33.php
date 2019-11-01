#!/usr/bin/php
<?php
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);

    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."pattern.php";
    include $_SESSION['CONF']['DIRS']['LIB']."sms.php";
    include $_SESSION['CONF']['DIRS']['LIB']."class.phpmailer.php";
    include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";

    $task = new controller();
    $process = $task->getCurrentTask();

    if (!(!empty($process) && count($process)>0))
	exit;

    $argv[1] = str_replace("'", "", $argv[1]);
    $argv[1] = substr($argv[1], 2);

    $from_mail = "ya.ksri@yandex.ru";
    $from_name = "Конструктор Империй";
    $subject = "Конструктор Империй";


        $mail = new PHPMailer(true);
        $mail->IsMail();
        $mail->SMTPDebug  = true;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = "smtp.yandex.ru";
        $mail->Port       = 465;
        $mail->Username   = $from_mail;
        $mail->Password   = 'xyz151617';
        $mail->CharSet    = "UTF-8";
        
        $text = "Телефон: test<BR>".
    		"Дата и время отклика: test2<BR>";


	$process['email_notify'] = "ya.ksri@yandex.ru";
        try {
		foreach (explode(";", $process['email_notify']) as $email)
	                $mail->AddAddress($email);
#                $mail->AddAddress($process['email_notify']);
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = "Новый отклик";
                $mail->MsgHTML($text);
                $mail->Send();
        } catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }

?>
