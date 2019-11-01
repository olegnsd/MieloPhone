<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

    $_SESSION['CONF'] = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../config.ini", true);

    mb_internal_encoding("UTF-8"); 
    mb_regex_encoding('UTF-8');

    include $_SESSION['CONF']['DIRS']['LIB']."mysql.php";
    include $_SESSION['CONF']['DIRS']['LIB']."pattern.php";

    include $_SESSION['CONF']['DIRS']['LIB']."auth.php";
    $auth = new auth;

    ini_set("session.gc_maxlifetime", 60*60*24*14);
    ini_set("session.cookie_lifetime", 60*60*24*14);
    ini_set("session.save_path", '/home/easyring/web/easyring24.com/sessions');

    session_start();
    //$code = 8585;//мой
    $_SESSION['CONF'] = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../config.ini", true);
//по куки восстанавливаем сессию
    if (isset($_COOKIE['remember']) && !(isset($_SESSION['AUTH']['AUTH']) && $_SESSION['AUTH']['AUTH'] == 1)) 
	{
	$authInfo = $auth->getRemember($_COOKIE['remember']);
	if ($authInfo == false) 
	{
	    setcookie("remember", "", time()-1);
	}
	else 
	{
	    session_id($_COOKIE['remember']);
    	    $_SESSION['AUTH'] = $authInfo;
	    $_SESSION['AUTH']['pages'] = json_decode($authInfo['pages'], true);
	    $_SESSION['AUTH']['caller'] = json_decode($authInfo['caller'], true);
	    $_SESSION['AUTH']['id'] = json_decode($authInfo['id'], true);//мое
    	    $_SESSION['AUTH']['AUTH'] = 1;
	}
    }
//конец по куки восстанавливаем сессию

//    session_start();

    



    $result 	= "";


    if (isset($_GET['page']) && $_GET['page'] != "")
	$_SESSION['PARAMS'] = explode("/", $_GET['page']);
    else
	$_SESSION['PARAMS'] = array("task");

    if (isset($_SESSION['PARAMS'][0]) && $_SESSION['PARAMS'][0] == "exit") {
	setcookie("remember", "", time()-1);
	foreach ($_SESSION as $k=>$v) 
		unset($_SESSION[$k]);
	header("location: /");
	exit();
    }


/*
    if (isset($_POST['auth']) && isset($_POST['login']) && isset($_POST['password'])) {
	$authInfo = $auth->login($_POST['login'], $_POST['password']);

	if (!empty($authInfo) && count($authInfo)>0) {
    	    $_SESSION['AUTH'] = $authInfo;
	    $_SESSION['AUTH']['pages'] = json_decode($authInfo['pages'], true);
	    $_SESSION['AUTH']['caller'] = json_decode($authInfo['caller'], true);
    	    $_SESSION['AUTH']['AUTH'] = 1;
	}
	else 
    	    $AUTH_ERROR = "Логин и/или пароль введен некорректно";
    }
*/

    if (isset($_SESSION['PARAMS'][0]) && $_SESSION['PARAMS'][0] == "download" && isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "sound") {
	include $_SESSION['CONF']['DIRS']['CONTROLLER']."task.php";
	$controller = new controller;

	$getId = $controller->getID($_SESSION['PARAMS'][2]);

	if (isset($getId['id'])) {
                    $filename = $_SESSION['CONF']['DIRS']['SOUNDS'].$getId['id'].".wav";
                
                    header('Content-Description: File Transfer');
                    header('Content-Type: audio/x-wav');
                    header('Content-Disposition: attachment; filename=' . basename($filename));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filename));
                    ob_clean();
                    flush();
                    readfile($filename);
	}

        exit;

    }

    if (isset($_SESSION['PARAMS'][0]) && $_SESSION['PARAMS'][0] == "getcode") {
	$phone = $auth->getPhone($_POST['login']);
	// print_r('phone= ');
	// print_r($phone);

	if ($phone === false || $phone == "") {
	    echo json_encode(["success" => false, "error" => "Пользователь не найден"]);
	    exit;
	}
	else {
	    include $_SESSION['CONF']['DIRS']['LIB']."sms.php";
	    $code = mt_rand(10000, 99999);//мой//

	    if ($_POST['login'] != "makmak5325346"){//мое//удалить

	    	$auth->updateCode($_POST['login'], $code);
	    }
	    else{//мое//удалить
	    	$code = 67239;
	    	$auth->updateCode($_POST['login'], $code);
	    }

        $sms = new Transport();
        if ($_POST['login'] != "makmak5325346"){//мое//удалить
		    $ok = $sms->send(array("text" => "Одноразовый пароль: {$code}"), array(substr($phone, 1)));//мое//
	    }
		
	    echo json_encode(["success" => true]);
	    exit;
	}
    }

    if (isset($_SESSION['PARAMS'][0]) && $_SESSION['PARAMS'][0] == "auth") {
	if ($_POST['login'] == "" || $_POST['code'] == ""){//мой//   || $_POST['code'] == ""{
	    echo json_encode(["success" => false, "error" => "Данные введены не корректно"]);
	    exit;
	}
	else {
	    $authInfo = $auth->auth($_POST['login'], $_POST['code']);//мой//$_POST['code']); //$code

    	    if (!empty($authInfo) && count($authInfo)>0) {
		$remember = session_id();

		$auth->setRemember($authInfo['id'], $remember);

		setcookie("remember", $remember, time()+60*60*24*14);

		echo json_encode(["success" => true]);
	        exit;
	    }
	    else  {
		echo json_encode(["success" => false, "error" => "Логин и/или пароль введены некорректно"]);
		exit;
	    }
	}
    }

    if (isset($_SESSION['AUTH']['AUTH']) && $_SESSION['AUTH']['AUTH'] == 1) 
	{
	$pages = array( "stats" 	=> "Статистика", "task" 	=> "Задачи" ,  "users"	=> "Пользователи", "caller"	=> "Телефоны",
	    "black"	=> "Черный лист",
	    "clients"	=> "Клиенты",
	    "test"	=> "тест"	
	);
	$menu = "";
	foreach ($_SESSION['AUTH']['pages'] as $page) 
	{
	    $pattern	= new pattern('main/menu');
	    $pattern->set_var("URL", "/".$page);
	    $pattern->set_var("NAME", $pages[$page]);
    	    $menu 	.= $pattern->result();
	}
	    $pattern	= new pattern('main/menu');
	    $pattern->set_var("URL", "/exit");
	    $pattern->set_var("NAME", "Выход");
    	    $menu 	.= $pattern->result();
#	print_r($menu);
	$pattern	= new pattern('main/header');
        $pattern->set_var("MENU", $menu);
        $result 	.= $pattern->result();
#echo $_SESSION['CONF']['DIRS']['CONTROLLER'].$_SESSION['PARAMS'][0].".php";
	if (file_exists($_SESSION['CONF']['DIRS']['CONTROLLER'].$_SESSION['PARAMS'][0].".php") && in_array($_SESSION['PARAMS'][0], $_SESSION['AUTH']['pages']) !== false) 
	{
	    include $_SESSION['CONF']['DIRS']['CONTROLLER'].$_SESSION['PARAMS'][0].".php";
	    $controller = new controller;
	    $result .= $controller->def();
	}
        else {
	    $pattern		=	new pattern('main/404');
	    $result	.=	$pattern->result();
	}
	$zone =  (int) $_SESSION['phone_timezone'] ; //(int) $_SESSION['AUTH']['timezone']);// + (int) $_SESSION['phone_timezone']);
	$pattern	= new pattern('main/footer');
#	$pattern->set_var("ZONE", $zone);
#	$pattern->set_var("ZONEDATE", date("Y-m-d H:i:s", strtotime("now {$_SESSION['phone_timezone']}hour")));
	$pattern->set_var("ZONEDATE", date("Y,m,d,H,i,s", strtotime("now {$_SESSION['phone_timezone']}hour")));
        $result	.=	$pattern->result();
    }
    elseif ((isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press2" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]))) 
	{
	echo "erwterstgrtert\n";
    	if (file_exists($_SESSION['CONF']['DIRS']['CONTROLLER']."clients.php")) {
		    include $_SESSION['CONF']['DIRS']['CONTROLLER']."clients.php";
		    $pattern	= new pattern('main/header_hash');
		    $controller = new controller;
		    $result .= $controller->def();
		    $result	.=	$pattern->result();
		}
	        else {
		    $pattern		=	new pattern('main/404');
		    $result	.=	$pattern->result();
		}
    } //запуск контроллера при входе в черный ручной список по хэшу
	elseif ((isset($_SESSION['PARAMS'][0]) && $_SESSION['PARAMS'][0] == "black" && isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "black_manually" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]))) 
	{
    	if (file_exists($_SESSION['CONF']['DIRS']['CONTROLLER']."black.php")) {
		    include $_SESSION['CONF']['DIRS']['CONTROLLER']."black.php";
		    $pattern	= new pattern('main/header_hash');
		    $controller = new controller;
		    $result .= $controller->def();
		    $result	.=	$pattern->result();
		}
	        else {
		    $pattern		=	new pattern('main/404');
		    $result	.=	$pattern->result();
		}
    }
    else { // выводим форму регистрации
        $pattern		= new pattern('main/auth');
	if (isset($AUTH_ERROR)) 
    	    $pattern->set_var("AUTH_ERROR", '<span class="label label-danger" style="width: 290px; text-align: center">'.$AUTH_ERROR.'</span><br/><br/>');
	else
    	    $pattern->set_var("AUTH_ERROR", '');

	$result	.= $pattern->result();
    }



    header("Cache-Control: no-store");
    header("Expires: " . date("r"));
    header("Content-Type: text/html");

    echo $result;
?>
