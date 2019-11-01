<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function getPhone($phone) {
	    $sql = "select * from black where phone = :phone";
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultRow();
	}

	function getPhone_man($phone) {
		$phone = mb_substr($phone, -10);
		if(strlen($phone) == 10){
			$sql = "select * from black_manually where phone LIKE '%$phone'";
		    $param[] = array(':phone', $phone, PDO::PARAM_STR);
		    return $this->query( $sql, $param )->resultRow();
		}else{
			return "";
		}  
	}

	function getPhone_man_all($id_first, $lenght) {
		$sql = "select * from black_manually where id >= $id_first LIMIT $lenght";
	    // $param[] = array(':id_first', $id_first, PDO::PARAM_INT);
	    // $param[] = array(':my_lenght', $lenght, PDO::PARAM_INT);
	    return $this->query( $sql, $param)->resultArray();
	}

	function getPhone_man_cnt() {
		$param[] = "";
		$sql = "select MAX(id) from black_manually";
	    $max = $this->query( $sql, $param )->resultRow();
	    $sql = "select MIN(id) from black_manually";
	    $min = $this->query( $sql, $param )->resultRow();
	    $sql = "select COUNT(id) from black_manually";
	    $count = $this->query( $sql, $param )->resultRow();
	    return json_encode(array($max["MAX(id)"], $min["MIN(id)"], $count["COUNT(id)"]));
	}

	function delete($phone) {
	    $sql = "delete from black where phone = :phone";
	    $param[] = array(':phone', $phone, PDO::PARAM_STR);
	    return $this->query( $sql, $param );
	}

	function delete_man($phone) {
		$phone = mb_substr($phone, -10);
		if ($phone != "" or !$phone) {
			$sql = "delete from black_manually where phone LIKE '%$phone'";
		    $param[] = array(':phone', $phone, PDO::PARAM_STR);
		    return $this->query( $sql, $param );
		}
	    return;
	}

	function press($id) {//мое
	    $sql = "select * from users where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    return $this->query( $sql, $param )->resultRow();
	}

	function hash_id($hash) {//мое
	    $sql = "select id, pass from users where hash = :hash";
	    $param[] = array(':hash', $hash, PDO::PARAM_STR);
	    return $this->query( $sql, $param )->resultRow();
	}

	function slect_user($id) {
	    $sql = "select * from users where id = :id";
	    $param[] = array(':id', $id, PDO::PARAM_INT);
	    $row = $this->query( $sql, $param )->resultRow();
	    return $row;
	}

	function insert_manually($phone){
		$sql = "insert into black_manually (phone) VALUES (:phone)";
		$param[] = array(':phone', $phone, PDO::PARAM_STR);
		$this->query( $sql, $param );
	}
	

    }

?>