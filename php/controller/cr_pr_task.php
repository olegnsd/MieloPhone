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
	    $tasks = $this->query->getList();
	    //var_dump($tasks);
    	if(isset($tasks['prior'])){
			echo ("Priority уже есть");
			exit();
		}
		else{
			$this->query->add_column_prior();
			echo("Priority добавлен");
		}
	}
}

