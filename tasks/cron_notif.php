#!/usr/bin/php

<?php
    $_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);

    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');
    require_once $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    require_once $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";

    $task = new controller();

    $notif = $task->getIdNotif();

    $notif_user = $task->getNotifTask($notif['task_id']); 

    if($notif_user['state'] == 'stop' || $notif_user['state'] == 'delete' || $notif_user['state'] == 'send' || $notif_user['send'] >= $notif_user['total']){
        //запустить новое задание
        if($curl = curl_init()){
       
            $query = array(
                'success' => 'success',
                '$max_dateadd' => $notif_user['dateadd'],
                'user_id' => $notif['user_id'],
                'hash' => '2f69e9e841dc',
            );

            curl_setopt($curl, CURLOPT_URL, 'https://bills.holding.bz/croncall.php');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query); 

            $out = curl_exec($curl);

            curl_close($curl);
        }
    }

    
