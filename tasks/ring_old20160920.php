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

    $from_mail = "ki0001026@gmail.com";
    $from_name = "Конструктор Империй";
    $subject = "Конструктор Империй";

#    require_once('/var/virtual/eks.me/www/startup_var.php');
#    require_once('/var/virtual/eks.me/www/includes/functions_sms.php');
#    require_once('/var/virtual/eks.me/www/classes/ssms_su.php');
#    include_once("/var/www/class/class.phpmailer.php");

    $ids = array();
    foreach ($process as $p)
	$ids[] = $p['id'];

    $process = $task->pressButtonContact($ids, $argv[1]);

    if ($process['sms_enable'] == 1) {
        $sms = new Transport();
        $ok = $sms->send(array("text" => $process['sms_text']), array($argv[1]));
    }

    if ($process['email_enable'] == 1) {
	$contact = $task->getContact($process['id'], $argv[1]);

        $mail = new PHPMailer(true);

        $mail->IsMail();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 465;
        $mail->Username   = "ki0001026@gmail.com";
        $mail->Password   = 'abc999cba';
        $mail->CharSet    = "UTF-8";

        try {
                $mail->AddAddress($contact['email']);
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = $subject;
                $mail->MsgHTML($process['email_text']);
                $mail->Send();
        } catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }
        
    if ($process['email_notify'] != "") {
        $mail = new PHPMailer(true);

        $mail->IsMail();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 465;
        $mail->Username   = "ki0001026@gmail.com";
        $mail->Password   = 'abc999cba';
        $mail->CharSet    = "UTF-8";
        
        $text = "Телефон: ".$contact['phone']."<BR>".
    		"Дата и время отклика: ".date("d.m.Y H:i:s", strtotime($contact['datering']))."<BR>".
    		($process['sms_notify'] != ""? "Текст SMS: ".$process['sms_text']."<BR>": "").
    		"Звук: <a href='http://s.easyring24.com/task/view/{$process['id']}/sound'>http://s.easyring24.com/task/view/{$process['id']}/sound</a>";

        try {
                $mail->AddAddress($process['email_notify']);
                $mail->SetFrom($from_mail, $from_name);
                $mail->Subject = "Новый отклик";
                $mail->MsgHTML($text);
                $mail->Send();
        } catch (phpmailerException $e) {
                                echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
                                echo $e->getMessage(); //Boring error messages from anything else!
        }
    }
        
        
        
    }

?>
