<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function add_column_hash() {
	    $sql = "alter table clients add hash varchar(40)";
	    $param[] = "";
	    return $this->query( $sql, $param );
	}

	function getList() {
	    $sql = "select * from clients";
	    $param[] = "";
	    return $this->query( $sql, $param )->resultArray();
	}

	function update_hash($id, $hash) {
		$sql = "update clients set hash = :hash where id = :id";
		$param[] = array(':id', $id, PDO::PARAM_INT);
		$param[] = array(':hash', $hash, PDO::PARAM_STR);
		$this->query( $sql, $param );
	}
}