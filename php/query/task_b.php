<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function listTask($all = false) {
	    $callers = array();
	    if (in_array("users", $_SESSION['AUTH']['pages']) === false)
		    foreach ($_SESSION['AUTH']['caller'] as $c)
			$callers[] = " concat(',', caller, ',') like '%,{$c},%'";

#	    $sql = "select * from tasks where uid = :uid order by dateadd desc";
	    $sql = "select * from tasks where (".(!empty($callers) && count($callers) > 0? implode(" or ", $callers): "1").") ".(!$all? " AND send/total < 1 and state != 'stop'": "")." order by dateadd desc";
#echo $sql;
#exit;
	    $param[] = array(':uid', $_SESSION['AUTH']['id'], PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultArray();
	}

	function edit($id) {
	    $callers = array();
	    if (in_array("users", $_SESSION['AUTH']['pages']) === false)
		    foreach ($_SESSION['AUTH']['caller'] as $c)
			$callers[] = " concat(',', caller, ',') like '%,{$c},%'";

	    $sql = "select * from tasks where id = :id".($_SESSION['AUTH']['user'] == "admin"? "": " AND (".(!empty($callers) && count($callers) > 0? implode(" or ", $callers): "1").")");
#echo $sql;
#exit;
	    $param[] = array(':id', $id, PDO::PARAM_INT);
#	    if ($_SESSION['AUTH']['user'] != "admin")
#	    	$param[] = array(':uid', $_SESSION['AUTH']['id'], PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function editCron($id) {
	    $sql = "select t.*, u.timezone from tasks t, callers u where t.id = :id and t.caller = u.mark";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function save($fields) {
	    if (isset($fields['id'])) {
		$sql = "update tasks set client_id = :client_id, sleep = :sleep, sms_enable = :sms_enable, email_enable = :email_enable, sms_text = :sms_text, email_text = :email_text, timefrom = :timefrom, timeto = :timeto, prior = :prior, comment = :comment, email_notify = :email_notify, url_notify = :url_notify  where id = :id ".(in_array("users", $_SESSION['AUTH']['pages']) === false? "and uid = :uid": "");
		if (in_array("users", $_SESSION['AUTH']['pages']) === false)
			$param[] = array(':uid', $_SESSION['AUTH']['id'], PDO::PARAM_INT);
		$param[] = array(':id', $fields['id'], PDO::PARAM_INT);
		$param[] = array(':sleep', $fields['sleep'], PDO::PARAM_INT);
		$param[] = array(':timefrom', $fields['timefrom'], PDO::PARAM_STR);
		$param[] = array(':timeto', $fields['timeto'], PDO::PARAM_STR);
		$param[] = array(':prior', $fields['prior'], PDO::PARAM_INT);
		$param[] = array(':sms_enable', $fields['sms_enable'], PDO::PARAM_INT);
		$param[] = array(':email_enable', $fields['email_enable'], PDO::PARAM_INT);
		$param[] = array(':client_id', $fields['client_id'], PDO::PARAM_INT);
		$param[] = array(':sms_text', $this->escape($fields['sms_text']), PDO::PARAM_STR);
		$param[] = array(':email_text', $this->escape($fields['email_text']), PDO::PARAM_STR);
		$param[] = array(':comment', $this->escape($fields['comment']), PDO::PARAM_STR);
		$param[] = array(':email_notify', $this->escape($fields['email_notify']), PDO::PARAM_STR);
		$param[] = array(':url_notify', $this->escape($fields['url_notify']), PDO::PARAM_STR);
#		$param[] = array(':client_id', $fields['client_id'], PDO::PARAM_INT);
		$this->query( $sql, $param );
#print_r($sql);
#print_r($param);
#print_r($fields);
#exit;
//		return $fields['id'];
	    }
	    else {
		$sql = "insert into tasks (uid, dateadd, caller, sms_enable, email_enable, sms_text, email_text, sleep, timefrom, timeto, prior, comment, email_notify, url_notify, client_id) VALUES (:uid, :dateadd, :caller, :sms_enable, :email_enable, :sms_text, :email_text, :sleep, :timefrom, :timeto, :prior, :comment, :email_notify, :url_notify, :client_id)";
		$param[] = array(':uid', $_SESSION['AUTH']['id'], PDO::PARAM_INT);
		$param[] = array(':dateadd', date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$param[] = array(':sleep', $fields['sleep'], PDO::PARAM_INT);
		$param[] = array(':timefrom', $fields['timefrom'], PDO::PARAM_STR);
		$param[] = array(':timeto', $fields['timeto'], PDO::PARAM_STR);
		$param[] = array(':prior', $fields['prior'], PDO::PARAM_INT);
		$param[] = array(':caller', $fields['caller'], PDO::PARAM_STR);
		$param[] = array(':sms_enable', $fields['sms_enable'], PDO::PARAM_INT);
		$param[] = array(':email_enable', $fields['email_enable'], PDO::PARAM_INT);
		$param[] = array(':sms_text', $this->escape($fields['sms_text']), PDO::PARAM_STR);
		$param[] = array(':email_text', $this->escape($fields['email_text']), PDO::PARAM_STR);
		$param[] = array(':comment', $this->escape($fields['comment']), PDO::PARAM_STR);
		$param[] = array(':email_notify', $this->escape($fields['email_notify']), PDO::PARAM_STR);
		$param[] = array(':url_notify', $this->escape($fields['url_notify']), PDO::PARAM_STR);
		$param[] = array(':client_id', $fields['client_id'], PDO::PARAM_INT);
		$fields['id'] = $this->query( $sql, $param )->get_last_id();
	    }

	    if ($fields['client_id'] > 0) {
#		unset($param);
#		$sql = "select * from tasks where id = :id";
#		$param[] = array(':id', $fields['id'], PDO::PARAM_INT);
#	    	$rowset = $this->query( $sql, $param )->resultArray();
#print_r($rowset);
#print_r($this->sql_error());
#exit;
		unset($param);
		$sql = "select client_id, count(*) as total from tasks t, tasks_base b where t.id = b.tid and t.client_id > 0 group by t.client_id, t.id";
		$param[] = array(':client_id', $fields['client_id'], PDO::PARAM_INT);
	    	$rowset = $this->query( $sql, $param )->resultArray();
#print_r($rowset);
#exit;

		foreach ($rowset as $row) {
			unset($param);
			$sql = "update clients set `all` = :all where id = :client_id";
			$param[] = array(':client_id', $row['client_id'], PDO::PARAM_INT);
			$param[] = array(':all', $row['total'], PDO::PARAM_INT);
	    		$this->query( $sql, $param );
#print_r($this->sql_query);

#echo $sql."<BR>";
#print_r($param);
#echo "<BR><BR>";
		}

#exit;
	    }

	    return $fields['id'];
	}

	function addBase($id, $name, $phone, $email) {	
	    $sql = "insert into tasks_base (tid, name, email, phone) VALUES (:tid, :name, :email, :phone)";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':name', $this->escape($name), PDO::PARAM_STR);
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':email', $email, PDO::PARAM_STR);
	    $this->query( $sql, $param );
	}

	function updateTaskBase($id, $total) {	
	    $sql = "update tasks set total = :total, send = :send where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $param[] = array(':total', $total, PDO::PARAM_INT);
	    $param[] = array(':send', 0, PDO::PARAM_INT);
	    $this->query( $sql, $param );
	}

	function deleteTaskBase($id) {
	    $sql = "delete from tasks_base where tid = :tid";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $this->query( $sql, $param );
	}

	function doublingBase($id, $oldID) {
	    $sql = "insert into tasks_base (tid, name, email, phone) select :tid, name, email, phone from tasks_base where tid = :oldTID";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':oldTID', $oldID, PDO::PARAM_INT);
	    $this->query( $sql, $param );
	}

	function getTaskForProcessing($caller, $prior) {
	    $sql = "	SELECT
			  t.*
			FROM
			  tasks t,
			  callers c
			where
			  t.state in (:state, :state2) and
			  t.total > 0 and
			  t.prior = :prior and
			  ".($caller != ""? " ({$caller}) AND": "")."
			  CAST(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL c.timezone HOUR), '%H%s') as SIGNED) >= CAST(concat(SUBSTRING(t.timefrom, 1, 2), SUBSTRING(t.timefrom, 4, 2)) as SIGNED) AND
			  CAST(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL c.timezone HOUR), '%H%s') as SIGNED) <= CAST(concat(SUBSTRING(t.timeto, 1, 2), SUBSTRING(t.timeto, 4, 2)) as SIGNED) and
			  c.mark = t.caller and 
			  c.active = 'yes'
			order by
			  dateadd asc
			limit 1";

// print_r('<br>'. 'sql=  ');
// echo $sql;
#exit;
#	    $sql = "select * from tasks where state in (:state, :state2) and total > 0 ".($caller != ""? " and ({$caller})": "")." order by dateadd asc limit 1";
	    $param[] = array(':state', 'new', PDO::PARAM_STR);
	    $param[] = array(':state2', 'start', PDO::PARAM_STR);
	    $param[] = array(':prior', $prior, PDO::PARAM_INT);
#	    $param[] = array(':state3', 'current', PDO::PARAM_STR);
#	    $this->query( $sql, $param )->resultRow();	    
#echo $sql;
	    $row = $this->query( $sql, $param )->resultRow();
#    	    print_r($this->lastQuery());exit;
	    return $row;
#print_r($row);
#	    if ($this->isCallerActive($row['caller']))
#		return $row;

	    return array();
	}

	function getCurrentTask() {
	    $sql = "select * from tasks where state = :state order by dateadd asc ";
	    $param[] = array(':state', 'current', PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultArray();
	}

	function getCurrentTask2() {
	    $sql = "select * from tasks where id = 1047 ";
	    $param[] = array(':state', 'current', PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultArray();
	}

	function getListResponse($id, $page = 0, $limit = 10) {
	    $sql = "select b.*, if(isnull(i.id), 0, count(*)) as total from tasks_base_info i
			right join (
				select * from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") order by datering desc limit :page, :limit
			) as b on (b.id = i.pid)
			group by b.id
			order by b.datering desc, b.phone asc";
	    $param[] = array(':limit', $limit, PDO::PARAM_INT);
	    $param[] = array(':page', $page*$limit, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultArray();
	}

	function getListResponseStat($id, $page = 0, $limit = 10) {
	    $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).")";
	    $total = $this->query( $sql, $param )->resultRow();
	    $total = $total['total'];

	    $sql = "select count(*) as total from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") and state='ANSWERED'";
	    $total2 = $this->query( $sql, $param )->resultRow();
	    $total2 = $total2['total'];

	    return array($total, $total2);
	}


	function getListResponseOnlyInterest($id, $page = 0, $limit = 1000) {
	    $sql = "select b.*, if(isnull(i.id), 0, count(*)) as total from tasks_base_info i
			right join (
				select * from tasks_base where tid in (".(!is_numeric($id)? implode(",", $id): $id).") and press = :press and send = :send order by datering desc limit :page, :limit 
			) as b on (b.id = i.pid)
			group by b.id
			order by b.datering desc";

	    $param[] = array(':press', 'Y', PDO::PARAM_STR);
	    $param[] = array(':send', 'Y', PDO::PARAM_STR);
	    $param[] = array(':limit', $limit, PDO::PARAM_INT);
	    $param[] = array(':page', $page*$limit, PDO::PARAM_INT);

	    return $this->query( $sql, $param )->resultArray();
	}

	function updateTaskState($id, $state) {
	    $sql = "update tasks set state = :state where id = :id";
	    $param[] = array(':state', $state, PDO::PARAM_STR);
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function getBase($id, $count) {
	    $sql = "select * from tasks_base where tid = :tid and send = :send limit :limit";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':limit', $count, PDO::PARAM_INT);
	    $param[] = array(':send', 'N', PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultArray();
	}

	function updateBaseSend($id, $send) {
	    $sql = "update tasks_base set send = :newsend, datering = :datering, total = total + 1 where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $param[] = array(':newsend', $send, PDO::PARAM_STR);
	    $param[] = array(':datering', date("Y-m-d H:i:s"), PDO::PARAM_STR);
	    $this->query( $sql, $param );
	}

	function updateTaskSend($id, $count) {
	    $sql = "update tasks set send = send + :count where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $param[] = array(':count', $count, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultArray();
	}

	function getContact($id, $phone) {
	    $sql = "select * from tasks_base where tid = :tid and phone = :phone limit 1";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultRow();
	}

	function issetContant($id, $phone) {
	    $sql = "select count(*) as total from tasks_base where tid = :tid and phone = :phone and datering between :date1 and :date2";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':date1', date("Y-m-d H:i:s", strtotime("NOW -30minute")), PDO::PARAM_STR);
	    $param[] = array(':date2', date("Y-m-d H:i:s", strtotime("NOW +30minute")), PDO::PARAM_STR);
	    $row = $this->query( $sql, $param )->resultRow();
	    return ($row['total'] == 0? false: true);
	}

	function issetContant2($id, $phone) {
	    $sql = "select count(*) as total from tasks_base where tid = :tid and phone = :phone and datering between :date1 and :date2";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':date1', date("Y-m-d H:i:s", strtotime("NOW -30minute")), PDO::PARAM_STR);
	    $param[] = array(':date2', date("Y-m-d H:i:s", strtotime("NOW +30minute")), PDO::PARAM_STR);
	    $row = $this->query( $sql, $param )->resultRow();
	    
	    return ($row['total'] == 0? false: true);
	}

	function pressButtonContact($id, $phone) {
	    $sql = "update tasks_base set press = :press where tid = :tid and phone = :phone";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':press', 'Y', PDO::PARAM_STR);
	    
	    $this->query( $sql, $param );


		unset($param);
		$sql = "select * from tasks where id = :id";
		$param[] = array(':id', $info['tid'], PDO::PARAM_INT);
		$task = $this->query( $sql, $param )->resultRow();

		if ($task['client_id'] > 0) {
		    unset($param);
		    $sql = "update clients set press = press + 1 where id = :id limit 1";
		    $param[] = array(':id', $task['client_id'], PDO::PARAM_INT);
		    $this->query( $sql, $param );
		}

	    unset($param);
	    $sql = "update tasks set press = press + 1 where id = :id limit 1";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $this->query( $sql, $param );
	}

	function getNoStateTaskBase($id) {
	    $sql = "select * from tasks_base where tid = :tid and (isnull(state) or state = '');";
	    $param[] = array(':tid', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultArray();
	}

	function getAskeriskRingState($phone, $date) {
	    $sql = "select * from asteriskcdrdb.cdr where src like :phone and calldate >= :date";
	    $param[] = array(':phone', "%{$phone}%", PDO::PARAM_STR);
	    $param[] = array(':date', $date, PDO::PARAM_STR);
	    $row = $this->query( $sql, $param )->resultRow();

/*
echo "\n===\n";
print_r($row);
echo "\n===\n";
print_r($this->lastQuery());
echo "\n===\n";
print_r($this->get_error());
echo "\n===\n";
*/
	    return $row;
	}

	function updateRingState($info, $state) {
	    $sql = "update tasks_base set state = :state, total = total + 1 where id = :id limit 1";
	    $param[] = array(':id', $info['id'], PDO::PARAM_INT);
	    $param[] = array(':state', $state, PDO::PARAM_STR);
	    $this->query( $sql, $param );

		unset($param);
		$sql = "select * from tasks where id = :id";
		$param[] = array(':id', $info['tid'], PDO::PARAM_INT);
		$task = $this->query( $sql, $param )->resultRow();

	    if ($state == "ANSWERED") {
			unset($param);
			if ($task['client_id'] > 0) {
			    unset($param);
			    $sql = "update clients set up = up + 1 where id = :id limit 1";
			    $param[] = array(':id', $task['client_id'], PDO::PARAM_INT);
			    $this->query( $sql, $param );
			}
	    }

	   
	    unset($param);
	    $sql = "update callers set last_call = '".date("Y-m-d H:i:s")."' where mark = :mark";
	    $param[] = array(':mark', $task['caller'], PDO::PARAM_STR);
	    $this->query( $sql, $param );

	    $phone = strpos($info['phone'], "+") !== false? substr($info['phone'], strpos($info['phone'], "+")+1): $info['phone'];

	    unset($param);
	    $sql = "insert into black (phone, client) values (:phone, :client)";
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':client', $task['client_id'], PDO::PARAM_STR);
	    $this->query( $sql, $param );
	}

	function getRingInfo($id) {
	    $sql = "select * from tasks_base where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

        function getCallerList() {
            $sql = "select * from callers order by name asc";
            return $this->query( $sql )->resultArray();
        }

        function isBlackPhone($phone, $client) {
	    $phone = strpos($phone, "+") !== false? substr($phone, strpos($phone, "+")+1): $phone;

            $sql = "select count(*) as total from black where phone = :phone and client = :client";
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    $param[] = array(':client', $client, PDO::PARAM_STR);
            return $this->query( $sql, $param )->resultRow();
        }

	function isUserTaskBase($uid, $pid) {
		if ($_SESSION['AUTH']['user'] == "admin")
			return true;

#		$sql = "select count(*) as total from tasks t, tasks_base b where b.id = :pid and b.tid = t.id and t.uid = :uid";
		$sql = "select count(*) as total from tasks t, tasks_base b, users u where b.id = :pid and b.tid = t.id and u.id=:uid and u.caller like  concat('%', t.caller, '%');";
	    	$param[] = array(':pid', $pid, PDO::PARAM_STR);
	    	$param[] = array(':uid', $uid, PDO::PARAM_STR);
            	$row = $this->query( $sql, $param )->resultRow();
		return $row['total'] == 0? false: true;
	}

	function taskBase($id) {
		$sql = "select * from tasks_base b where b.id = :id";
	    	$param[] = array(':id', $id, PDO::PARAM_STR);
            	return $this->query( $sql, $param )->resultRow();
	}

	function taskBaseInfo($id) {
		$sql = "select * from tasks_base_info where pid = :id";
	    	$param[] = array(':id', $id, PDO::PARAM_STR);
            	return $this->query( $sql, $param )->resultArray();
	}

	function addTaskBaseInfo($pid, $text) {
		$sql = "insert into tasks_base_info (pid, text) VALUES (:pid, :text)";
		$param[] = array(':pid', $pid, PDO::PARAM_INT);
		$param[] = array(':text', $text, PDO::PARAM_STR);
		$this->query( $sql, $param );
	}

	function isCallerActive($mark) {
		$sql = "select active from callers where mark = :mark";
	    	$param[] = array(':mark', $mark, PDO::PARAM_STR);
            	$active = $this->query( $sql, $param )->resultRow();

		return $active['active'] == "yes"? true: false;
	}

	function callerInfo($mark) {
		$sql = "select * from callers where mark = :mark";
	    	$param[] = array(':mark', $mark, PDO::PARAM_STR);
            	return $this->query( $sql, $param )->resultRow();
	}

	function userTimezone($uid) {
		$sql = "select timezone from users where id = :id";
	    	$param[] = array(':id', $uid, PDO::PARAM_INT);
            	$row = $this->query( $sql, $param )->resultRow();
		return $row['timezone'];
	}

	function addActiveState($cid, $state) {
		$sql = "insert into callers_logs (cid, state) VALUES (:cid, :state)";
	    	$param[] = array(':cid', $cid, PDO::PARAM_INT);
	    	$param[] = array(':state', $state, PDO::PARAM_INT);
            	$this->query( $sql, $param );
	}

	function getCalls($phone) {
	    $sql = "select * from calls where phone = :phone order by date desc";
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultArray();
	}


	function getID($hash) {
	    $sql = "select id from tasks where md5(md5(id)) = :hash";
	    $param[] = array(':hash', $hash, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultRow();
	}

	function getClients() {
	    $sql = "select * from clients order by name asc";
	    return $this->query( $sql, $param )->resultArray();
	}

	function getTasksByClient($client_id) {
	    $param = [];
	    $sql = "select * from tasks where client_id = {$client_id}";
	    return $this->query( $sql, $param )->resultArray();
	}

	function getClientName($client_id) {
	    $param = [];
	    $sql = "select * from clients where id = {$client_id}";
	    $result = $this->query( $sql, $param )->resultRow();
	    return $result['name'];
	}

    }

?>