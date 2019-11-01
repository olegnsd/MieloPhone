<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

    $_SESSION['CONF'] = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../config.ini", true);

    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."pattern.php";

    //include $_SESSION['CONF']['DIRS']['LIB']."auth.php";
    //$auth = new auth;

    ini_set("session.gc_maxlifetime", 60*60*24*14);
    ini_set("session.cookie_lifetime", 60*60*24*14);
    ini_set("session.save_path", '/home/easyring/web/easyring24.com/sessions');

    session_start();
    //$code = 8585;//мой
    $_SESSION['CONF'] = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../config.ini", true);

    $_SESSION['PARAMS'] = "cr_hs_users";//explode("/", $_GET['page']);

    include $_SESSION['CONF']['DIRS']['CONTROLLER'].$_SESSION['PARAMS'].".php";

    $controller = new controller;
	$controller->def();
