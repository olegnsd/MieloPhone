<?php
    class controller {
	private $query;
	private $myxor;
	// construncor
	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    //include $_SESSION['CONF']['DIRS']['LIB']."myxor.php";
	    $this->query = new query_controller();
	    //$this->myxor = new xor_controller();
	}
	    
	public function def() {
	    $result = "";

	    if ((isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "edit" && isset($_SESSION['PARAMS'][2]) && is_numeric($_SESSION['PARAMS'][2])) || (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add")) 
		{
		if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "edit") 
		{
	                $user = $this->query->edit($_SESSION['PARAMS'][2]);
	                if (!(!empty($user) && count($user)>0))
        	            header("Location: /caller");
		}

                $pattern        = new pattern('clients/edit');
                $pattern->set_var("NAME", (isset($user)? $user['name']: ""));
                $pattern->set_var("PICKED", (isset($user)? $user['picked']: ""));
                $pattern->set_var("ID", (isset($user)? $user['id']: 0));

                $result .= $pattern->result();

	    }
	    elseif ((isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]))) {//мое  && is_numeric($_SESSION['PARAMS'][2]) || (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add")

	    	$client_id = $this->query->hash_id($_SESSION['PARAMS'][2]);
	    	$client_id = $client_id["id"];

			if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press") {
	            $user = $this->query->press($client_id);
	            if (!(!empty($user) && count($user)>0))
		            header("Location: /caller");
			}

			$uid = $_SESSION['AUTH']['id'];

			$client = $this->query->edit($client_id);

			$tasks = $this->query->getTasksByClientUser($uid, $client_id);

			$allClientsT = array_map(function($task) {
		                return $task['id'];
		    }, $tasks); 

			if (!empty($allClientsT) && count($allClientsT) > 0) {
				list($total1, $total2, $total3, $total4) = $this->query->getListResponseStat($allClientsT);
			}
			else {
				$total1 = $total2 = $total3 = $total4 = 0;
			}

            $pattern        = new pattern('clients/press_header');
            $pattern->set_var("TITLE", 'Отклики клиента для этого пользавателя');
            $pattern->set_var("CLIENT", $client['name']);
            $pattern->set_var("PICKED", $user['picked']);
            $pattern->set_var("ALL", $total1);
			$pattern->set_var("UP", $total2);
			$pattern->set_var("PRESS", $total3);
			$pattern->set_var("CALLS", $total4);

			$result	.= $pattern->result();			
			
			if (!empty($tasks) && count($tasks)>0) {
			    foreach ($tasks as $task) {

			        $tasksBase = $this->query->getTasksBaseByTasks($task['id']);

					list($total1, $total2, $total3, $total4) = $this->query->getListResponseStat($clientTask['id']);


					$pattern	= new pattern('clients/press_row');
					$pattern->set_var("NAME", $clientTask['comment']);
					$pattern->set_var("DATE", $clientTask['dateadd']);
					$pattern->set_var("ALL", $total1);
					$pattern->set_var("UP", $total2);
					$pattern->set_var("PRESS", $total3);
					$pattern->set_var("CALLS", $total4);
					$result	.= $pattern->result();

					if($total3 > 0){
						$pattern	= new pattern('clients/press_row2');
						$result	.= $pattern->result();
						foreach ($tasksBase as $taskBase) {
							$pattern	= new pattern('clients/press_row_inline');
							$pattern->set_var("PHONE", $taskBase['phone']);
							$pattern->set_var("DATERING", $taskBase['datering']);
							$result	.= $pattern->result();	
						}
						$pattern	= new pattern('clients/press_row3');
						$result	.= $pattern->result();
					}		
			    }
			}
			else {
	        	$pattern	= new pattern('clients/info');
				$pattern->set_var("DATE", "");
				$pattern->set_var("TEXT", "Нет информации об откликах");
				$result	.= $pattern->result();
		    }
			
			$pattern	= new pattern('clients/press_footer');
			$result	.= $pattern->result();
	    }
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press2" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]) && isset($_SESSION['PARAMS'][3]) && $_SESSION['PARAMS'][3] == "export") {
			$client_id = $this->query->hash_id($_SESSION['PARAMS'][2]);
	    	$client_id = $client_id["id"];
				if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press2") {
		            $user = $this->query->press($client_id);
		            if (!(!empty($user) && count($user)>0))
			            header("Location: /caller");
				}
				
				$client = $this->query->edit($client_id);
				$clientTasks = $this->query->getTasksByClient($client_id);// task by client
				
				$foptmp = fopen("/tmp/".$client['name'].".csv", "ab");
				if (!empty($clientTasks) && count($clientTasks)>0) {
					$phoneResp = array();
					
				    foreach ($clientTasks as $clientTask) {
						//массив для импорта в файл
				        $tasksBase = $this->query->getTasksBaseByTasks($clientTask['id']);
						$tmp_phone = array_column($tasksBase, 'phone');
						foreach($tmp_phone as $phone){
	//						mb_substr($phone, 3);
							fwrite($foptmp, mb_substr($phone, 2) . PHP_EOL);
						}
						
						$phoneResp = array_merge($phoneResp, $tmp_phone);
					}
				}
				fclose($foptmp);
				//controller::file_force_download("/tmp/".$client['name'].".csv");
				
				$file = "/tmp/".$client['name'].".csv";
				if (file_exists($file)) {
				    header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename="'.basename($file).'"');
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($file));
				    readfile($file);
				    exit;
				}
			}
	    elseif ((isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press2" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]))) {//мое  || (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add")
	    	$client_id = $this->query->hash_id($_SESSION['PARAMS'][2]);
	    	$client_id = $client_id["id"];

			if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press2") {
	            $user = $this->query->press($client_id);
	            if (!(!empty($user) && count($user)>0))
		            header("Location: /caller");
			}

			$uid = $_SESSION['AUTH']['id'];

			$client = $this->query->edit($client_id);

			$clientTasks = $this->query->getTasksByClient($client_id);// task by client

			$allClientsT = array_map(function($task) {
		                return $task['id'];
		        }, $clientTasks); 

			if (!empty($allClientsT) && count($allClientsT) > 0) {
				list($total1, $total2, $total3, $total4) = $this->query->getListResponseStat($allClientsT);
			}
			else {
				$total1 = $total2 = $total3 = $total4 = 0;
			}



            $pattern        = new pattern('clients/press_header');
            $pattern->set_var("TITLE", 'Отклики клиента для всех пользователей');
            $pattern->set_var("CLIENT", $client['name']);
            $pattern->set_var("PICKED", $user['picked']);
            $pattern->set_var("ALL", $total1);
			$pattern->set_var("UP", $total2);
			$pattern->set_var("PRESS", $total3);
			$pattern->set_var("CALLS", $total4);
			$pattern->set_var("HASH", $_SESSION['PARAMS'][2]);

			$result	.= $pattern->result();
			
			if (!empty($clientTasks) && count($clientTasks)>0) {
					$phoneResp = array();
			    foreach ($clientTasks as $clientTask) {
							//массив для импорта в файл
			        $tasksBase = $this->query->getTasksBaseByTasks($clientTask['id']);
							$tmp_phone = array_column($tasksBase, 'phone');
							$phoneResp = array_merge($phoneResp, $tmp_phone);

					list($total1, $total2, $total3, $total4) = $this->query->getListResponseStat($clientTask['id']);

					$pattern	= new pattern('clients/press_row');
					$pattern->set_var("NAME", $clientTask['comment']);
					$pattern->set_var("DATE", $clientTask['dateadd']);
					$pattern->set_var("ALL", $total1);
					$pattern->set_var("UP", $total2);
					$pattern->set_var("PRESS", $total3);
					$pattern->set_var("CALLS", $total4);
					$result	.= $pattern->result();

					if($total3 > 0){
						$pattern	= new pattern('clients/press_row2');
						$result	.= $pattern->result();
						foreach ($tasksBase as $taskBase) {
							$pattern	= new pattern('clients/press_row_inline');
							$pattern->set_var("PHONE", $taskBase['phone']);
							$pattern->set_var("DATERING", $taskBase['datering']);
							$result	.= $pattern->result();	
						}
						$pattern	= new pattern('clients/press_row3');
						$result	.= $pattern->result();
					}		
			    }
			}
			else {
	        	$pattern	= new pattern('clients/info');
				$pattern->set_var("DATE", "");
				$pattern->set_var("TEXT", "Нет информации об откликах");
				$result	.= $pattern->result();
		    }
			
			$pattern	= new pattern('clients/press_footer');
			$result	.= $pattern->result();
	    }
	    elseif ((isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press3" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2]))) {//мое  && is_numeric($_SESSION['PARAMS'][2]) || (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add")
	    	$client_id = $this->query->hash_id($_SESSION['PARAMS'][2]);
	    	$client_id = $client_id["id"];

			if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "press3") {
	            $user = $this->query->press($client_id);
	            if (!(!empty($user) && count($user)>0))
		            header("Location: /caller");
			}

			$uid = $_SESSION['AUTH']['id'];

			$client = $this->query->edit($client_id);

            $pattern        = new pattern('clients/press_header');
            $pattern->set_var("TITLE", 'Отклики клиента skype');
            $pattern->set_var("CLIENT", $client['name']);

			$result	.= $pattern->result();

			$clientTasks = $this->query->getTasksBaseByTasks_skype($client_id);// task by client

		        // $clientsTasks = array_map(function($task) {
		        //         return $task['id'];
		        // }, $clientsTasks); 
			
			
			if (!empty($clientTasks) && count($clientTasks)>0) {
			    foreach ($clientTasks as $clientTask) {

			        //$tasksBase = $this->query->getListResponseStat2($clientTask['id']);

					$pattern	= new pattern('clients/press_row');
					$pattern->set_var("NAME", $clientTask['comment']);
					$pattern->set_var("DATE", $clientTask['dateadd']);
					$result	.= $pattern->result();

					//foreach ($tasksBase as $taskBase) {
						$pattern	= new pattern('clients/press_row_inline');
					
						//$tasksBaseInfo = $this->query->getTasksBaseInfoByTasksBase($taskBase['id']);
						$pattern->set_var("PHONE", $clientTask['phone']);
						$pattern->set_var("DATERING", $clientTask['datering']);
						$result	.= $pattern->result();	
					//}
					$pattern	= new pattern('clients/press_row2');
					$result	.= $pattern->result();		
			    }
			}
			else {
	        	$pattern	= new pattern('clients/info');
				$pattern->set_var("DATE", "");
				$pattern->set_var("TEXT", "Нет информации об откликах");
				$result	.= $pattern->result();
		    }
			
			$pattern	= new pattern('clients/press_footer');
			$result	.= $pattern->result();
	    }
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "save") {
		$this->query->save($_POST);
		header("Location: /clients");
	    }
	    else {	    
		$pattern	= new pattern('clients/list_header');
		$result	.= $pattern->result();

		$list = $this->query->getList();//clients all
		
		if (!empty($list) && count($list)>0) {
		    $num = 0;
		    foreach ($list as $c) {

		        $clientsTasks = $this->query->getTasksByClient($c['id']);// task by client

		        $clientsTasks = array_map(function($task) {
		                return $task['id'];
		        }, $clientsTasks); 

			if (!empty($clientsTasks) && count($clientsTasks) > 0) {
				list($total1, $total2, $total3, $total4) = $this->query->getListResponseStat($clientsTasks);
			}
			else {
				$total1 = $total2 = $total3 = $total4 = 0;
			}

			$pattern	= new pattern('clients/list_row');
			$pattern->set_var("NAME", $c['name']);
			$pattern->set_var("PICKED", $c['picked']);
			$pattern->set_var("ALL", $total1);
			$pattern->set_var("UP", $total2);
			$pattern->set_var("PRESS", $total3);
			$pattern->set_var("CALLS", $total4);
//			$pattern->set_var("BUTTONS", '');
			$pattern->set_var("BUTTONS", '<a href="/clients/edit/'.$c['id'].'" class="btn btn-xs btn-default" title="Изменить параметры телефона"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>');

			//$hash = md5($_SESSION['CONF']['SALT']['SALT1'] . $c['id'] . $_SESSION['CONF']['SALT']['SALT2']);

			$pattern->set_var("BUTTONS2", '<a href="/clients/press/'.$c['hash'].'" class="btn btn-xs btn-default" title="Отклики этого пользователя"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>');//мое
			$pattern->set_var("BUTTONS3", '<a href="/clients/press2/'.$c['hash'].'" class="btn btn-xs btn-default" title="Отклики всех пользователей"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>');//мое
			// $pattern->set_var("BUTTONS4", '<a href="/clients/press3/'.$c['id'].'" class="btn btn-xs btn-default" title="Отклики клиента skype"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>');//мое
			$result	.= $pattern->result();			
		    }
		}
		
		$pattern	= new pattern('clients/list_footer');
		$result	.= $pattern->result();
	    }

	    return $result;
	}
	
	private function file_force_download($file) {
	  if (file_exists($file)) {
		// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		// если этого не сделать файл будет читаться в память полностью!
		if (ob_get_level()) {
		  ob_end_clean();
		}
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		// читаем файл и отправляем его пользователю
		readfile($file);
		exit;
	  }
	}


    }

?>
