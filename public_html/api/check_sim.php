<?php
ini_set('display_errors', 0);

$_SESSION['CONF'] = parse_ini_file(__DIR__."/../../config.ini", true);

require_once $_SESSION['CONF']['DIRS']['LIB']."mysql.php";

$db = new sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);

$jsonStr = file_get_contents("php://input"); //read the HTTP body.
$sim = json_decode($jsonStr, true);

if($sim['token'] != '98j38fhc') die('token false');

$active_sims = '';
if($sim['simgoip']){
    $simgoip = $sim['simgoip'];
    $simgoip = implode(",", $simgoip);
    $active_sims = $db->sql_query("SELECT mark, balance, active FROM callers WHERE mark IN ({$simgoip})")->resultArray();// AND active = 'yes' AND balance > 0
}

$phone_pay = '';
if($sim['chanell_pay']){
    $chanell_pay = $sim['chanell_pay'];
    $phone_pay = $db->sql_query("SELECT phone FROM callers WHERE mark='$chanell_pay'")->resultRow();
}


$active_out = array();
foreach($active_sims as $key => $active_sim){
    $active_out['mark'][] = $active_sim['mark'];
    $active_out['balance'][] = $active_sim['balance'];
    if($active_sim['active'] == "yes"){
    	$active_out['active'][] = 1;
    }else {
    	$active_out['active'][] = 0;
    }
}
preg_match('/\d{10,}/', $phone_pay['phone'], $phone);
$phone = clear_fone($phone[0]);

$active_out['phone_pay'] =$phone;
$active_out['true'] = 'true';

echo(json_encode($active_out));

function clear_fone($person_phone){
	$phone = str_replace(" ", "", $person_phone);
	$tmpphone = str_replace("+", "", $phone);
	if(strlen($tmpphone) == 10 or strlen($tmpphone) == 11){
		if(strlen($tmpphone) == 11){
			$phone = substr_replace($tmpphone, "", 0, 1);
		}
		else{
			$phone = $tmpphone;//"7" . $tmpphone;
		}
	}else $phone = false;

	return $phone;
}
