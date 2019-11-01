<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function getList() {
	    $sql = "select * from callers order by name asc";
	    return $this->query( $sql, $param )->resultArray();
	}

	function edit($id) {
	    $sql = "select * from callers where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function save($fields) {
	    if (isset($fields['id'])) {
		$sql = "update callers set name = :name, phone = :phone, timezone = :timezone, email = :email, send = :send  where id = :id";
		$param[] = array(':id', $fields['id'], PDO::PARAM_INT);
		$param[] = array(':name', $fields['name'], PDO::PARAM_STR);
		$param[] = array(':phone', $fields['phone'], PDO::PARAM_STR);
		$param[] = array(':email', $fields['email'], PDO::PARAM_STR);
		$param[] = array(':send', $fields['send'], PDO::PARAM_INT);
		$param[] = array(':timezone', $fields['timezone'], PDO::PARAM_STR);
		$this->query( $sql, $param );
	    }
	    else {
		$sql = "insert into callers (name, mark, sound, phone, timezone, email, send) VALUES (:name, :mark, :sound, :phone, :timezone, :email, :send)";
		$param[] = array(':name', $fields['name'], PDO::PARAM_STR);
		$param[] = array(':mark', $fields['mark'], PDO::PARAM_STR);
		$param[] = array(':sound', $fields['sound'], PDO::PARAM_STR);
		$param[] = array(':phone', $fields['phone'], PDO::PARAM_STR);
		$param[] = array(':email', $fields['email'], PDO::PARAM_STR);
		$param[] = array(':send', $fields['send'], PDO::PARAM_INT);
		$param[] = array(':timezone', $fields['timezone'], PDO::PARAM_STR);
		$this->query( $sql, $param );
	    }
	}

	function getRings($caller, $datefrom, $dateto) {
	    $sql = "select date_format(b.datering, '%Y-%m-%d-%H') as date, count(*) as total from tasks t, tasks_base b where t.id = b.tid and b.datering between :datefrom and :dateto and t.caller = :caller group by date_format(b.datering, '%Y%m%d%H')";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':caller', $caller, PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}

	function getActiveRings($caller, $datefrom, $dateto) {
	    $sql = "select date_format(b.datering, '%Y-%m-%d-%H') as date, count(*) as total from tasks t, tasks_base b where t.id = b.tid and b.datering between :datefrom and :dateto and t.caller = :caller and b.state = :state group by date_format(b.datering, '%Y%m%d%H')";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':caller', $caller, PDO::PARAM_STR);
	    $param[] = array(':state', 'ANSWERED', PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}

	function getSuccessRings($caller, $datefrom, $dateto) {
	    $sql = "select date_format(b.datering, '%Y-%m-%d-%H') as date, count(*) as total from tasks t, tasks_base b where t.id = b.tid and b.datering between :datefrom and :dateto and t.caller = :caller and b.press = :press group by date_format(b.datering, '%Y%m%d%H')";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':caller', $caller, PDO::PARAM_STR);
	    $param[] = array(':press', 'Y', PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}

	function getBlackRings($caller, $datefrom, $dateto) {
	    $sql = "select date_format(b.datering, '%Y-%m-%d-%H') as date, count(*) as total from tasks t, tasks_base b where t.id = b.tid and b.datering between :datefrom and :dateto and t.caller = :caller and b.send = :send group by date_format(b.datering, '%Y%m%d%H')";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':caller', $caller, PDO::PARAM_STR);
	    $param[] = array(':send', 'F', PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}

	function getActivesCaller($cid, $datefrom, $dateto) {
	    $sql = "select date_format(datetime, '%Y-%m-%d-%H-%i') as datetime, state from callers_logs where cid = :cid and datetime between :datefrom and :dateto order by datetime asc";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':cid', $cid, PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}


	function getBalanceCaller($cid, $datefrom, $dateto) {
	    $sql = "select date_format(datetime, '%Y-%m-%d-%H-%i') as datetime, balance from callers_active where cid = :cid and datetime between :datefrom and :dateto order by datetime asc";
	    $param[] = array(':datefrom', $datefrom, PDO::PARAM_STR);
	    $param[] = array(':dateto', $dateto, PDO::PARAM_STR);
	    $param[] = array(':cid', $cid, PDO::PARAM_STR);

	    return $this->query( $sql, $param )->resultArray();
	}

    }

?>