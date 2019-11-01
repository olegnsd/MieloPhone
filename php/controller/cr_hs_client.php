<?php
    class controller {
	private $query;
	// construncor
	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    //include $_SESSION['CONF']['DIRS']['LIB']."myxor.php";
	    $this->query = new query_controller();
	    //$this->myxor = new xor_controller();
	}
	    
	public function def() {
	    $clients = $this->query->getList();
		if(isset($clients[0]['hash'])){
			echo ("Hash уже есть");
			exit();
		}
		else{
			$this->query->add_column_hash();
			foreach ($clients as $client) {
				$hash = md5($_SESSION['CONF']['SALT']['SALT1'] . $client['id'] . $_SESSION['CONF']['SALT']['SALT2']);
				$this->query->update_hash($client['id'], $hash);
				echo("hash добавлен");
			}
		}
	}
}

	