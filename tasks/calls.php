#!/usr/bin/php
<?php
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);

    mb_internal_encoding("UTF-8");
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."sms.php";
    include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";

    $task = new controller();

    $db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);

    if (date("H") < 3)
	$datefrom = date("d.m.Y", strtotime("now -1day"));
    else
	$datefrom = date("d.m.Y");

#    $datefrom = date("d.m.Y", strtotime("now -1month"));
    $calls = $task->getStatsSipuni($datefrom, "");

    if (!empty($calls) && count($calls) > 0) {
	foreach ($calls as $c) {
	    if (strlen($c[5]) == 11)
		$phone = substr($c[5], 1);
	    else
		$phone = $c[5];

	    $total = $db->query("select count(*) as total from tasks_base where phone = '{$phone}' and press='Y'")->resultRow();

	    if ($total['total'] > 0) {
		$db->query("insert into calls (phone, date, filename) VALUES ('{$phone}', '".date("Y-m-d H:i:s", strtotime($c[2]))."', '{$c[11]}')");
        
           mail('kasper@poltavamail.com',
                         'test333',
                         '<html><body>test33'.$process['url_notify'].'<br>'.$query.'</body></html>',
                         "From: dev@kaspersoft.in\r\n" 
                             ."Content-type: text/html; charset=utf-8\r\n"
                             ."X-Mailer: PHP mail script"
                        );
        
		$task->downloadSipuniFile($c[11]);
	    }
	}
    }

?>