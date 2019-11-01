#!/usr/bin/php
<?php
$_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);
require_once $_SESSION['CONF']['DIRS']['LIB']."mysql.php";                                                                                                               
$mysqli = new  mysqli($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
$results = $mysqli->query("select * from  tasks where state='current';");

while($row = $results->fetch_assoc()) {
//print_r($row);
$file=fopen($_SESSION['CONF']['DIRS']['HOME']."task_pid/".$row['id'],"r");
$pid=(int)fgets($file);
#echo "$pid\n";
if(!(is_dir("/proc/$pid")))
$no_proc_arr[]=$row['id'];

}

if(isset($no_proc_arr))
foreach ($no_proc_arr as  $no_proc)
{echo $no_proc;
$results = $mysqli->query("update  tasks set  state='start' where id='$no_proc';");                                                                                                    
}



$peer_tmp=explode("\n",`sudo asterisk -x 'sip show peers'`);
foreach($peer_tmp as $peer)
{
$tmp=preg_split("/[\s\/]+/", $peer);
if (isset($tmp[8])) $peers[$tmp[0]]=$tmp[8];
}

$results = $mysqli->query("select * from  tasks where state='pause';"); 

while( $row = $results->fetch_assoc())  
   if($peers[$row['caller']]=="OK") $task_on_id[]=$row['id'];

if(isset($task_on_id))
foreach ($task_on_id as $id)                                                                                                                                          
{echo "$id\n";
$results = $mysqli->query("update  tasks set  state='start' where id='$id';");                                                                                          
}

$results = $mysqli->query("select * from tasks where state='start' or state='pause' order by caller,prior");




          






