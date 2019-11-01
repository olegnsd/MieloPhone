#!/usr/bin/php
<?php
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);


    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."pattern.php";
    include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";


#    $db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
#    $db->sql_query("use ring;");
#    $db->sql_query("update tasks set state='new' where id= 52;");
#    $db->sql_query("update tasks_base set send = 'N', total = 0, state=NULL where tid=52;");

    $task = new controller();

    $sounds = $task->getCallerList();

    $process = $task->getTaskForProcessing();
#print_r($process);
#echo "@@@";
#exit;
    if (!(!empty($process) && count($process) > 0))
	exit;

    $callers = explode(",", $process['caller']);
    $caller = 0;

    if (file_exists($_SESSION['CONF']['DIRS']['SOUNDS'].$process['id'].".wav"))
	foreach ($callers as $c)
	    copy($_SESSION['CONF']['DIRS']['SOUNDS'].$process['id'].".wav", $_SESSION['CONF']['DIRS']['ASTSOUNDS'].$sounds[$c].".wav");

    $template = file_get_contents(__DIR__."/template.tpl");

    function createRing($contact, $caller, $template) {
	$send = str_replace("{!PHONE!}", $contact['phone'], str_replace("{!CALLER!}", $caller, $template));

	file_put_contents($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], $send);
        chmod($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], 0777);
	chown ($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], "asterisk");
        chgrp ($_SESSION['CONF']['DIRS']['ASTERISK'].$contact['id'], "asterisk");
    }

    $task->updateTaskState($process['id'], "current");


    while (true) {
	$base = $task->getBase($process['id']);
        $count = 0;
	if (!empty($base) && count($base) > 0) {
	    foreach ($base as $contact) {
		if ($task->isBlackPhone($contact['phone'])) {
		    $task->updateBaseSend($contact['id'], 'F');
		    $task->updateTaskSend($process['id'], 1);
		    continue;
		}

		createRing($contact, $callers[$caller], $template);

		$count++;
		$caller++;
		if ($caller >= count($callers))
		    $caller = 0;

		$task->updateBaseSend($contact['id'], 'Y');
	        $task->updateTaskSend($process['id'], 1);

		$state = $task->edit($process['id']);
		if ($state['state'] != "current")
			exit;

		sleep($process['sleep']);
	    }
	}
	else {
	    break;
	}
    }

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

print_r($info);
echo "!!!";
print_r($getRingState);

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

		    sleep(60);
		}
	    }
	}
    }

    sleep(120); // ждем две минуты, чтобы дошли все звонки
    $task->updateTaskState($process['id'], "send");

?>
