<?php

	require_once $_SESSION['CONF']['DIRS']['LIB']."recaptchalib.php";

    class controller {
	private $query;

	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    $this->query = new query_controller();
	}
	    
	public function def() {
	    $result = "";

	    $hash = $this->query->slect_user($_SESSION['AUTH']['id']);

	    //удалить из автосписка
	    if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "delete") {
			$_SESSION['PARAMS'][2] = str_replace(" ", "+", $_SESSION['PARAMS'][2]);
			$this->query->delete($_SESSION['PARAMS'][2]);
		
			$phone = $_SESSION['PARAMS'][2];
			$pattern	= new pattern('black/search');
			$pattern->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern2	= new pattern('black/success');
			$pattern2->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern->set_var("DATA", $pattern2->result());
			$pattern->set_var("DATA_MAN", "");
			$pattern->set_var("DATA_ALL", "");

			$pattern->set_var("BUTTONS1", '<a href="/black/black_manually/'.$hash['hash'].'" class="btn btn-xxs btn-default" title="Добавить телефон">Добавить телефон вручную</span></a>');
			$result	.= $pattern->result();
		
	    }
	    //удалить из ручного списка
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "delete_man") {
			$_SESSION['PARAMS'][2] = str_replace(" ", "+", $_SESSION['PARAMS'][2]);
			$this->query->delete_man($_SESSION['PARAMS'][2]);

			$phone = $_SESSION['PARAMS'][2];
			$pattern	= new pattern('black/search');
			$pattern->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern2	= new pattern('black/success_man');
			$pattern2->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern->set_var("DATA", $pattern2->result());
			$pattern->set_var("DATA_MAN", "");
			$pattern->set_var("DATA_ALL", "");
			
			$pattern->set_var("BUTTONS1", '<a href="/black/black_manually/'.$hash['hash'].'" class="btn btn-xxs btn-default" title="Добавить телефон">Добавить телефон вручную</span></a>');
			$result	.= $pattern->result();
		
	    }
	    //удалить из автосписка и ручного списка
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "delete_all") {
			$_SESSION['PARAMS'][2] = str_replace(" ", "+", $_SESSION['PARAMS'][2]);
			$this->query->delete($_SESSION['PARAMS'][2]);
			$this->query->delete_man($_SESSION['PARAMS'][2]);

			$phone = $_SESSION['PARAMS'][2];
			$pattern	= new pattern('black/search');
			$pattern->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern2	= new pattern('black/success_all');
			$pattern2->set_var("PHONE", $_SESSION['PARAMS'][2]);
			$pattern->set_var("DATA", $pattern2->result());
			$pattern->set_var("DATA_MAN", "");
			$pattern->set_var("DATA_ALL", "");
			
			$pattern->set_var("BUTTONS1", '<a href="/black/black_manually/'.$hash['hash'].'" class="btn btn-xxs btn-default" title="Добавить телефон">Ручной черный список</span></a>');
			$result	.= $pattern->result();
		
	    }
	    //вход в ручной список по хэшу
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "black_manually" && isset($_SESSION['PARAMS'][2]) && is_string($_SESSION['PARAMS'][2])) {
	    	$hash = $_SESSION['PARAMS'][2];
	    	$user_id = $this->query->hash_id($hash);
	    	$pass_black = $user_id["pass"];
	    	$user_id = $user_id["id"];
			
            if (!(!empty($user_id) && count($user_id)>0))
	            header("Location: /caller");

	        //рекаптча
	        //if(!isset($_SESSION["recaptcha"])){
	        //	$pattern	= new pattern('black/recaptcha');
	        //	// ваш секретный ключ
			//	$secret = "6LcwhzYUAAAAAJ_gz9MaYcjZDWTg8TV7fOhdLuGP";
			//	// пустой ответ
			//	$response = null;
			//	// проверка секретного ключа
			//	$reCaptcha = new ReCaptcha($secret);
	        //	if(isset($_POST['g-recaptcha-response'])){
	        //		$response = $reCaptcha->verifyResponse(
			//	        $_SERVER["REMOTE_ADDR"],
			//	        $_POST["g-recaptcha-response"]
			//	    );
			//	    if ($response != null && $response->success) {
			//			$_SESSION["recaptcha"] = true;
			//			header("Location: /black/black_manually/$hash");
			//		}else{
			//			$pattern->set_var("DANGER", "<div class='alert alert-danger' role='alert'>Пройдите проверку на человека</div>");
			//		}
		    //    }
		    //    $pattern->set_var("DANGER", "");
	        //	$pattern->set_var("BUTTON1", '/black/black_manually/' . $hash );
	        //	$result	.= $pattern->result();
	        //	return $result;
	        //	exit;
		        
	        //}
			
			//добавить в ручной список        
			if(isset($_SESSION['PARAMS'][3]) && $_SESSION['PARAMS'][3] == "add"){
				$pattern	= new pattern('black/add_manually');
				if(isset($_POST['phone_man']) && $_POST['phone_man'] != ""){
					$phone_man = $_POST['phone_man'];
					
					$phone_man_cl = $this->clear_fone($phone_man);
					$phone_test = $this->query->getPhone_man($phone_man_cl);
					if(!empty($phone_test) && count($phone_test)>0){
						$pattern->set_var("ALERT", "<div class='alert alert-warning' role='alert'>Такой телефон $phone_man уже есть в ручном списке</div>"); 
					}else{
						$pattern->set_var("ALERT", "<div class='alert alert-success' role='alert'>Телефон $phone_man добавлен</div>"); 
						$this->query->insert_manually($phone_man_cl);
					}
					
				}
				elseif(isset($_POST['phone_man']) && $_POST['phone_man'] == ""){
					$pattern->set_var("ALERT", "<div class='alert alert-danger' role='alert'>Введите правильно телефон</div>");
				}
				$pattern->set_var("ALERT", "");
				$pattern->set_var("BUTTONS1", '/black/black_manually/' . $hash . '/add' );
				$pattern->set_var("BUTTONS2", '/black/black_manually/' . $hash);
				

			}
			//найти в ручном списке 
			elseif(isset($_SESSION['PARAMS'][3]) && $_SESSION['PARAMS'][3] == "search"){
				$pattern	= new pattern('black/search_manually');
				if(isset($_POST['phone_man']) && $_POST['phone_man'] != ""){
					$phone_man_cl = $this->clear_fone($_POST['phone_man']);
					$pattern->set_var("PHONE", $_POST['phone_man']);
					$phone_man = $this->query->getPhone_man($phone_man_cl);
					if (!empty($phone_man) && count($phone_man)>0) {
						$pattern2	= new pattern('black/found_man_hash');
						$pattern2->set_var("PHONE", $phone_man_cl);
						$pattern2->set_var("HASH", $hash);
						$pattern->set_var("DATA_MAN", $pattern2->result());
				    }
				    else {
						$pattern2	= new pattern('black/nofound_man');
						$pattern->set_var("DATA_MAN", $pattern2->result());
						$nofound = true;
				    }
				}
				$pattern->set_var("BUTTONS2", '/black/black_manually/' . $hash);
				$pattern->set_var("BUTTONS1", '/black/black_manually/' . $hash . '/search' );
				$pattern->set_var("DATA_MAN", "");
				$pattern->set_var("PHONE", "");
			}
			//удалить в ручном списке
			elseif (isset($_SESSION['PARAMS'][3]) && $_SESSION['PARAMS'][3] == "delete_man") {
				$query_line = $_SERVER['QUERY_STRING'];
				$phone = $_SESSION['PARAMS'][4];
				$phone = str_replace(" ", "+", $phone);
				$this->query->delete_man($phone);

				if(strpos($query_line, '!')){
					$page = substr($query_line, strpos($query_line, '!') + 1);
					header("Location: /black/black_manually/$hash/show_all/!$page");
					exit;
				}
				$pattern	= new pattern('black/search_manually');
				$pattern->set_var("PHONE", $phone);
				$pattern2	= new pattern('black/success_man');
				$pattern2->set_var("PHONE", $phone);
				$pattern->set_var("DATA_MAN", $pattern2->result());
				$pattern->set_var("BUTTONS2", '/black/black_manually/' . $hash);
				$pattern->set_var("BUTTONS1", '/black/black_manually/' . $hash. '/search' );
			
		    }
		    //показать ручной список
		    elseif(isset($_SESSION['PARAMS'][3]) && $_SESSION['PARAMS'][3] == "show_all"){
		    	//проверка пасс
		        if(isset($_POST["pass_black"]) && $_POST["pass_black"] != ""){
		        	if($_POST["pass_black"] == $pass_black){
		        		$_SESSION["pass_black"] = "true";
					}else{
						$_SESSION["pass_black"] = "false";
						header("Location: /black/black_manually/$hash");
					}   
		        }
		        elseif (isset($_SESSION["pass_black"]) && $_SESSION["pass_black"]=="false" ) {
		        	header("Location: /black/black_manually/$hash");
		        }elseif(!isset($_SESSION["pass_black"])){
		        	header("Location: /black/black_manually/$hash");
		        }

		    	$page_lenght = 1000;
		    	if (isset($_SESSION["pagin"])) {
		    		$page_lenght = $_SESSION["pagin"];
		    	}
		    	if (isset($_POST["pagin"])) {
		    		$page_lenght = $_POST["pagin"];
		    		if($page_lenght < 5)$page_lenght = 5;
		    		if($page_lenght > 1000)$page_lenght = 1000;
		    		$_SESSION["pagin"] = $page_lenght;
		    	}
		    	$query_line = $_SERVER['QUERY_STRING'];
		    	$page = 1;
		    	if(strpos($query_line, '!')){
		    		$page = substr($query_line, strpos($query_line, '!')+1);
				}
				$phone_pagin = json_decode($this->query->getPhone_man_cnt());
				$max_id = $phone_pagin[0];
				$min_id = $phone_pagin[1];
				$count = $phone_pagin[2];
				// echo "max: " . $max_id ."min: " . $min_id ."count: " . $count;
				$page_all = ceil($count/$page_lenght);
				if($page > $page_all) $page_all = $page;
				if($page < 1) $page = 1;

		    	$phone_all = $this->query->getPhone_man_all(($page-1)*$page_lenght+$min_id, $page_lenght);
		    	$pattern	= new pattern('black/show_all');
		    	$line = "";
		    	foreach ($phone_all as $phone) {
		    		$id = $phone['id'];
		    		$phone = $phone['phone'];
		    		$line = $line . "<tr><td>$id</td><td>$phone</td><td><a type='button' href='/black/black_manually/$hash/delete_man/$phone/!$page' class='btn btn-group-sm btn-danger '>Удалить</a></td></tr>";
		    	}
		    	if($page == $page_all){
		    		if($id < $max_id){
		    			$page_all++;
		    		}
		    	}
		    	$pattern->set_var("LINE", $line);

		    	$pagin = "";
		    	for ($i=1; $i <= $page_all ; $i++) { 
		    		$href = "/black/black_manually/$hash/show_all/!$i";
		    		if($i == $page){
		    			$pagin .= "<li class='active'><a>$i <span class='sr-only'>(current)</span></a></li>";
		    		}else{
		    			$pagin .= "<li><a href='$href'>$i</a></li>";
		    		}	 
		    	}
		    	$pattern->set_var("PAGIN", $pagin);

		    	$page_pr = $page-1;
		    	$page_nxt = $page+1;
		    	if($page_pr < 1){
		    		$pattern->set_var("PREW", "<li class='disabled'><a aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>");
		    	}else{
		    		$pattern->set_var("PREW", "<li><a href='/black/black_manually/$hash/show_all/!$page_pr' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>");
		    	}
		    	if($page_nxt > $page_all){
		    		$pattern->set_var("NEXT", "<li class='disabled'><a aria-label='Next'><span aria-hidden='true'>&laquo;</span></a></li>");
		    	}else{
		    		$pattern->set_var("NEXT", "<li><a href='/black/black_manually/$hash/show_all/!$page_nxt' aria-label='Next'><span aria-hidden='true'>&laquo;</span></a></li>");
		    	}		   

		    	$pattern->set_var("BUTTONS2", '/black/black_manually/' . $hash);
		    	$pattern->set_var("BUTTONS3", '/black/black_manually/' . $hash . '/show_all');
		    	$pattern->set_var("PAGIN_COUNT", $page_lenght);
		    	$pattern->set_var("PHONE_COUNT", $count);  
		    }else{
				$pattern	= new pattern('black/manually');
				$pattern->set_var("BUTTONS1", '/black/black_manually/' . $hash . '/add' );
				$pattern->set_var("BUTTONS2", '/black/black_manually/' . $hash . '/search' );
				$pattern->set_var("BUTTONS3", '/black/black_manually/' . $hash. '/show_all' );
				$pattern->set_var("ALERT", "");
			}
			$result	.= $pattern->result();
	    }
	    else {	//найти в автоматич списке и ручном   
			$pattern	= new pattern('black/search');
			$pattern->set_var("PHONE", (isset($_POST['phone'])? $_POST['phone']: ""));
			$pattern->set_var("BUTTONS1", '<a href="/black/black_manually/'.$hash['hash'].'" class="btn btn-xxs btn-default" title="Добавить телефон">Добавить телефон вручную</span></a>');

			if (isset($_POST['phone'])) {
			    $phone = $this->query->getPhone($_POST['phone']);
			    $phone_man = $this->query->getPhone_man($_POST['phone']);

			    $nofound = false;
			    if (!empty($phone) && count($phone)>0) {
					$pattern2	= new pattern('black/found');
					$pattern2->set_var("PHONE", $_POST['phone']);
					$pattern->set_var("DATA", $pattern2->result());
			    }
			    else {
					$pattern2	= new pattern('black/nofound');
					$pattern->set_var("DATA", $pattern2->result());
					$nofound = true;
			    }

			    if (!empty($phone_man) && count($phone_man)>0) {
					$pattern2	= new pattern('black/found_man');
					$pattern2->set_var("PHONE", $_POST['phone']);
					$pattern->set_var("DATA_MAN", $pattern2->result());
			    }
			    else {
					$pattern2	= new pattern('black/nofound_man');
					$pattern->set_var("DATA_MAN", $pattern2->result());
					$nofound = true;
			    }

			    if(!$nofound){
					$pattern2	= new pattern('black/found_all');
					$pattern2->set_var("PHONE", $_POST['phone']);
					$pattern->set_var("DATA_ALL", $pattern2->result());	
				}else{
					$pattern->set_var("DATA_ALL", "");
				}
			}else{
			    $pattern->set_var("DATA", "");
				$pattern->set_var("DATA_MAN", "");
				$pattern->set_var("DATA_ALL", "");
			}
			$result	.= $pattern->result();

	    }

	    return $result;
	}

	//очистка телефона
	private function clear_fone($person_phone){
		$phone = str_replace(array("+", " ", "(", ")", "-"), "", $person_phone);
		// if(strlen($tmpphone) == 10 or strlen($tmpphone) == 11){
		// 	if(strlen($tmpphone) == 11){
		// 		$phone = substr_replace($tmpphone, "+7", 0, 1);
		// 	}
		// 	else{
		// 		$phone = "+7" . $tmpphone;
		// 	}
		// }else $phone = false;
		// echo "person_phone:" . $person_phone . "\n";

		// $phone = preg_replace("\D+", "", $person_phone);

		return $phone;
	}

    }

?>
