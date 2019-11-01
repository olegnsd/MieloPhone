<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function add_column_prior() {
	    $sql = "ALTER TABLE `tasks` ADD `prior` TINYINT( 4 ) NOT NULL DEFAULT '0' AFTER `datestop`";
	    $param[] = "";
	    return $this->query( $sql, $param );
	}

	function getList() {
	    $sql = "select * from tasks limit 1";
	    $param[] = "";
	    return $this->query( $sql, $param )->resultRow();
	}

	function update_prior($id, $prior) {
		$sql = "update clients set prior = :prior where id = :id";
		$param[] = array(':id', $id, PDO::PARAM_INT);
		$param[] = array(':prior', $prior, PDO::PARAM_STR);
		$this->query( $sql, $param );
	}
}