<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function getList() {
	    $sql = "select * from clients order by name asc";
	    return $this->query( $sql, $param )->resultArray();
	}

	function edit($id) {
	    $sql = "select * from clients where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function press($id) {//мое
	    $sql = "select * from clients where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function hash_id($hash) {//мое
	    $sql = "select id from clients where hash = :hash";
	    $param[] = array(':hash', $hash, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultRow();
	}

	function save($fields) {
	    if ($fields['id'] > 0) {
		$sql = "update clients set name = :name, picked = :picked  where id = :id";
		$param[] = array(':id', $fields['id'], PDO::PARAM_INT);
		$param[] = array(':name', $fields['name'], PDO::PARAM_STR);
		$param[] = array(':picked', $fields['picked'], PDO::PARAM_INT);
		$this->query( $sql, $param );
	    }
	    else {
		$sql = "insert into clients (name, picked) VALUES (:name, :picked)";
		$param[] = array(':name', $fields['name'], PDO::PARAM_STR);
		$param[] = array(':picked', $fields['picked'], PDO::PARAM_INT);
		$this->query( $sql, $param );

		$last_id = $this->get_last_id();
		$hash = md5($_SESSION['CONF']['SALT']['SALT1'] . $last_id . $_SESSION['CONF']['SALT']['SALT2']);
		// var_dump($last_id);
		// var_dump($hash);
		// exit;
		$sql = "update clients set hash = :hash where id = :last_id";
		unset($param); 
		$param[] = array(':last_id', $last_id, PDO::PARAM_INT);
		$param[] = array(':hash', $hash, PDO::PARAM_STR);
		$this->query( $sql, $param );
	    }
	}

        function getTasksByClient($client_id) {
            $param = [];
            $sql = "select * from tasks where client_id = {$client_id}";
            return $this->query( $sql, $param )->resultArray();
        }

        function getListResponseStat($id) {
            $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).")";
            $total = $this->query( $sql, $param )->resultRow();
            $total = $total['total'];
            
            $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") and state='ANSWERED'";
            $total2 = $this->query( $sql, $param )->resultRow();
            $total2 = $total2['total'];

            $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") and press='Y'";
            $total3 = $this->query( $sql, $param )->resultRow();
            $total3 = $total3['total'];

            $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") and send='Y'";
            $total4 = $this->query( $sql, $param )->resultRow();
            $total4 = $total4['total'];

            return array($total, $total2, $total3, $total4);
        }

        function getTasksByClientUser($uid, $client_id) {
        	$sql = "select * from tasks where uid = :uid and client_id = :client_id";
			$param[] = array(':uid', $uid, PDO::PARAM_INT);
			$param[] = array(':client_id', $client_id, PDO::PARAM_INT);
			return $this->query( $sql, $param )->resultArray();
        }

        function getTasksBaseByTasks($task_id) {
        	$sql = "select * from tasks_base where tid = :task_id and press='Y'";
			$param[] = array(':task_id', $task_id, PDO::PARAM_INT);
			return $this->query( $sql, $param )->resultArray();
        }

   //      function getTasksBaseInfoByTasksBase($task_base_id) {
   //      	$sql = "select * from tasks_base_info where pid = :task_base_id";
			// $param[] = array(':task_base_id', $task_base_id, PDO::PARAM_INT);
			// return $this->query( $sql, $param )->resultArray();
   //      }

        // function getListResponseStat2($task_id) {
        // 	$sql = "select * from tasks_base where tid = :task_id and press='Y'";
        // 	$param[] = array(':task_id', $task_id, PDO::PARAM_INT);
        //     return $this->query( $sql, $param )->resultArray();
            
        // }

        function getTasksBaseByTasks_skype($client_id) {
        	$sql = "select t.id, t.comment, t.dateadd, b.datering, b.phone, b.name, b.email from tasks t join tasks_base b on(b.tid = t.id) where b.press='Y' and t.client_id = :client_id order by t.dateadd desc";
			$param[] = array(':client_id', $client_id, PDO::PARAM_INT);
			return $this->query( $sql, $param )->resultArray();
        }


    }

?>