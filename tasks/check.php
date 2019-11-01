#!/usr/bin/php
<?php
$_SESSION['CONF'] = parse_ini_file(__DIR__."/../config.ini", true);
require_once $_SESSION['CONF']['DIRS']['LIB']."mysql.php";                                                                                                               
$mysqli = new  mysqli($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }

        $loadav=sys_getloadavg();
        $dat=date("Y-m-d-h:i:sa");
        $file_log="/home/easyring/web/easyring24.com/tasks/check.log";
#       file_put_contents($file,$qaz_slow);
        
#если сильно нагружен
if ( $loadav[0] >2)
{
 $mysqli->query("update  tasks set  state='pause' where state='current';");
file_put_contents($file_log,$dat."  ".$loadav[0]);
 exit(1);
}


//проверка на отсутствия процессов на задание при current    
$results = $mysqli->query("select * from  tasks where state='current';");
while( $row = $results->fetch_assoc())
{
#for debugging
#$debug=0;
#if ($row['id'] == 7331)  { $debug=1; echo  "debug for task ".$row['id']."\n"; }


$file=fopen($_SESSION['CONF']['DIRS']['HOME']."task_pid/".$row['id'],"r");
$pid=(int)fgets($file);

if(!(is_dir("/proc/$pid"))) $no_proc_arr[]=$row['id'];
fclose($file);
}

// делаем старт там где нет процессов
if(isset($no_proc_arr))
foreach ($no_proc_arr as  $no_proc)
{echo $no_proc;
$results = $mysqli->query("update  tasks set  state='start' where id='$no_proc';");                                                                                                    
}



//собираем  каналы  со статусом  Ok в  массив callers

              $command = "/usr/local/bin/show_peers.sh";                                                                                                                                   
                exec ($command, $out);                                                                                                                                                       
                                                                                                                                                                                           
                $callers = array();                                                                                                                                                          
                for ($i = 1; $i < count($out)-1; $i++) 
		   {                                                                                                                                  
                        $name = substr($out[$i], 0, 25);                                                                                                                                     
                        if (strpos($name, "/") !== false)                                                                                                                                    
                                $name = substr($name, 0, strpos($name, "/"));                                                                                                                
                                                                                                                                                                                             
                        $state = substr($out[$i], 105, 10);                                                                                                                                  
#                        print_r($out[$i]);
                        if (strpos($state, " ") !== false)                                                                                                                                   
                                $state = substr($state, 0, strpos($state, " "));                                                                                                             
                                                                                                                                                                                             
                        if (trim($state) == "OK")                                                                                                                                            
                            $callers[trim($name)] = trim($state);                                                                                                                            
                                                                                                                                                                                             
                        $allCallers[trim($name)] = trim($state);                                                                                                                             
                        $allCallersIP[trim($name)] = trim(substr($out[$i], 25, 25));                                                                                                         
                }                          
#	print_r($callers);




//снятие с пауз когда канал в норме снова
$results = $mysqli->query("select tasks.id ,callers.mark,callers.active
from tasks join callers on tasks.caller=callers.mark
where tasks.state not in ('stop','current') and tasks.send/tasks.total < 1 and time(now()) between tasks.timefrom and tasks.timeto");
while( $row = $results->fetch_assoc())
   if($row['active']=="yes") $task_on_on[$row['id']]="start"; else $task_on_on[$row['id']]="pause";
if(isset($task_on_on))
    foreach ($task_on_on as $id=>$state)
	{
	$results = $mysqli->query("update  tasks set  state='$state' where id='$id';");                                                                                          
	file_put_contents($file_log,$dat."  resume task ".$id);
	}
	
//на паузу если канал упал

  // просмотр задач - если сaller статус != ok - то на паузу
//формируем массив задач что поставить на паузу  
$results = $mysqli->query("select * from  tasks where state in ('current','start') and send/total < 1 and time(now()) between timefrom and timeto ; ");
 while( $row = $results->fetch_assoc())
	if(!(isset($callers[$row['caller']]))) $task_pause_id[]=$row['id'];
//ставим паузу
//print_r($task_pause_id);
if(isset($task_pause_id))
foreach($task_pause_id as $id)
  {$mysqli->query("update  tasks set  state='pause' where id='$id';");
  	file_put_contents($file_log,$dat."  pause task ".$id." goip ".$row['caller'] );
  	}




//приоритеты

$results = $mysqli->query("select * from tasks where
  state not in ('stop')  and send/total < 1  and time(now()) between timefrom and timeto order by caller,prior,total-send");
while($row = $results->fetch_assoc()) 
$task_priors[$row['caller']]=$row['id'];
if(isset($task_priors))
foreach($task_priors as $caller=>$id)
$mysqli->query("update  tasks set  state='pause'
where caller='$caller' and id <>'$id' and state not in ('stop')  and send/total < 1  and time(now()) between timefrom and timeto" );


//НА паузу по времени
$mysqli->query("update  tasks set  state='pause'
where  state not in ('stop')  and not time(now())  between timefrom and timeto" );


exit(0);
