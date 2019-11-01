<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function getList() {
	    $sql = "select * from users order by user asc";
	    return $this->query( $sql, $param )->resultArray();
	}

	function getCallerList() {
	    $sql = "select * from callers order by name asc";
	    return $this->query( $sql, $param )->resultArray();
	}

	function edit($id) {
	    $sql = "select * from users where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $row = $this->query( $sql, $param )->resultRow();
	    $row['caller'] = json_decode($row['caller'], true);
	    $row['pages'] = json_decode($row['pages'], true);
	    return $row;
	}

	function save($fields) {
	    if (!isset($fields['pages'])) $fields['pages'] = array();
	    if (!isset($fields['caller'])) $fields['caller'] = array();

	    if (isset($fields['id'])) {
		$sql = "update users set user = :user, phone = :phone, pages = :pages, caller = :caller, timezone = :timezone".($fields['pass'] != ""? ", pass = :pass": "")." where id = :id";
		$param[] = array(':id', $fields['id'], PDO::PARAM_INT);
		$param[] = array(':timezone', $fields['timezone'], PDO::PARAM_STR);
		$param[] = array(':caller', json_encode($fields['caller']), PDO::PARAM_STR);
		$param[] = array(':pages', json_encode($fields['pages']), PDO::PARAM_STR);
		$param[] = array(':user', $fields['user'], PDO::PARAM_STR);
		$param[] = array(':phone', $fields['phone'], PDO::PARAM_STR);
		if ($fields['pass'] != "")
		    $param[] = array(':pass', $fields['pass'], PDO::PARAM_STR);
		$this->query( $sql, $param );
	    }
	    else {
		$sql = "insert into users (user, pass, pages, caller, timezone, phone, hash) VALUES (:user, :pass, :pages, :caller, :timezone, :phone, :hash)";
		$param[] = array(':user', $this->escape($fields['user']), PDO::PARAM_STR);
		$param[] = array(':pass', $this->escape($fields['pass']), PDO::PARAM_STR);
		$param[] = array(':timezone', $fields['timezone'], PDO::PARAM_STR);
		$param[] = array(':pages', json_encode($fields['pages']), PDO::PARAM_STR);
		$param[] = array(':caller', json_encode($fields['caller']), PDO::PARAM_STR);
		$param[] = array(':phone', $fields['phone'], PDO::PARAM_STR);
		$hash = md5($_SESSION['CONF']['SALT']['SALT1'] . $last_id . $_SESSION['CONF']['SALT']['SALT2']);
		$param[] = array(':hash', $hash, PDO::PARAM_STR);
		$this->query( $sql, $param );

	    }
	}

	function delete ($id) {
	    $sql = "delete from users where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $this->query( $sql, $param );

	    $sql = "select id from tasks where uid = :id";
	    $rowset = $this->query( $sql, $param )->resultArray();

	    if (!empty($rowset) && count($rowset) > 0) {
		foreach ($rowset as $row) {
		    unset($param);
		    $sql = "delete from tasks_base where tid = :tid";
		    $param[] = array(':tid', $row['id'], PDO::PARAM_INT);
		    $this->query( $sql, $param );
		}

		unset($param);
		$sql = "delete from tasks where uid = :uid";
		$param[] = array(':uid', $id, PDO::PARAM_INT);
		$this->query( $sql, $param );
	    }
	}

    }

?>