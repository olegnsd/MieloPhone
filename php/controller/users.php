<?php
    class controller {
	private $query;

	private $hours = array (
		"-1" => "Калининград",
		"+0" => "Москва",
#		"+1" => "Самара",
		"+2" => "Екатеринбург",
		"+3" => "Омск",
		"+4" => "Красноярск",
		"+5" => "Иркутск",
		"+6" => "Якутск",
		"+7" => "Владивосток",
		"+8" => "Республика Саха",
		"+9" => "Камчатка",
	);

	// construncor
	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    $this->query = new query_controller();
	}
	    
	public function def() {
	    $result = "";

	    if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "edit" && isset($_SESSION['PARAMS'][2]) && is_numeric($_SESSION['PARAMS'][2])) {
                $user = $this->query->edit($_SESSION['PARAMS'][2]);

                if (!(!empty($user) && count($user)>0))
                    header("Location: /users");

		$callers = $this->query->getCallerList();

                $pattern        = new pattern('users/edit');
                $pattern->set_var("USER", $user['user']);
                $pattern->set_var("PHONE", $user['phone']);
                $pattern->set_var("PAGESSTATS", in_array("stats", $user['pages']) !== false? "CHECKED": "");
                $pattern->set_var("PAGESBLACK", in_array("black", $user['pages']) !== false? "CHECKED": "");
                $pattern->set_var("PAGESTASK", in_array("task", $user['pages']) !== false? "CHECKED": "");
                $pattern->set_var("PAGESUSERS", in_array("users", $user['pages']) !== false? "CHECKED": "");
                $pattern->set_var("PAGESCALLER", in_array("caller", $user['pages']) !== false? "CHECKED": "");
                $pattern->set_var("PAGESCLIENTS", in_array("clients", $user['pages']) !== false? "CHECKED": "");

		$hoursText = "";
		foreach ($this->hours as $h=>$nameHour) 
			$hoursText .= '<option value="'.$h.'" '.($user['timezone'] == $h? "SELECTED": "").'>'.$nameHour." ({$h}:00)".'</option>';
		$pattern->set_var("HOURS", $hoursText);

		$caller = "";

		foreach ($callers as $c)
		    $caller .= '<label><input type="checkbox" name="caller[]" value="'.$c['mark'].'" '.(in_array($c['mark'], $user['caller']) !== false? "CHECKED": "").'> '.$c['name'].'</label>&nbsp;';

                $pattern->set_var("CALLERS", $caller);
                $pattern->set_var("ID", $user['id']);
                $result .= $pattern->result();

	    }
            elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "delete" && isset($_SESSION['PARAMS'][2]) && is_numeric($_SESSION['PARAMS'][2])) {
		$this->query->delete($_SESSION['PARAMS'][2]);
		header("Location: /users");
            }
            elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add") {
                $pattern        = new pattern('users/add');
		$callers = $this->query->getCallerList();
		$caller = "";

		foreach ($callers as $c)
		    $caller .= '<label><input type="checkbox" name="caller[]" value="'.$c['mark'].'" '.(in_array($c['mark'], $user['caller']) !== false? "CHECKED": "").'> '.$c['name'].'</label>&nbsp;';

                $pattern->set_var("CALLERS", $caller);

		$hoursText = "";
		foreach ($this->hours as $h=>$nameHour) 
			$hoursText .= '<option value="'.$h.'" '.($h == "+0"? "SELECTED": "").'>'.$nameHour." ({$h}:00)".'</option>';
		$pattern->set_var("HOURS", $hoursText);


                $result .= $pattern->result();
            }
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "save") {
		$this->query->save($_POST);
		header("Location: /users");
	    }
	    else {	    
		$pattern	= new pattern('users/list_header');
		$result	.= $pattern->result();

		$listUsers = $this->query->getList();
		$caller = $this->query->getCallerList();
		
		if (!empty($listUsers) && count($listUsers)>0) {
		    $num = 0;
		    foreach ($listUsers as $user) {
			$pattern	= new pattern('users/list_row');
			$pattern->set_var("USER", $user['user']);
			$pattern->set_var("CALLER", $this->_viewCaller($caller, $user['caller']));
			$pattern->set_var("PAGES", $this->_viewPages($user['pages']));
			$pattern->set_var("BUTTONS", '<a href="/users/edit/'.$user['id'].'" class="btn btn-xs btn-default" title="Изменить параметры пользователя"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>&nbsp;'.
						     '<a tabindex="-1" href="#myModal" data-toggle="modal" onClick="del('.$user['id'].')" class="btn btn-xs btn-default" title="Удалить пользователя"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>');
			$result	.= $pattern->result();			
		    }
		}
		
		$pattern	= new pattern('users/list_footer');
		$result	.= $pattern->result();
	    }

	    return $result;
	}

        function _viewCaller($callers, $caller) {
            $result = "";

	    $caller = json_decode($caller, true);

	    foreach ($callers as $c) {
		if (in_array($c['mark'], $caller) !== false)
		    $result .= '<span class="label label-success">'.$c['name'].'</span>&nbsp;';
	    }

            return $result;
        }

        function _viewPages($pages) {
            $result = "";

	    $pagesSuccess = array(
		"stats" => "Статистика",
		"users" => "Пользователи",
		"task" => "Задачи",
		"caller" => "Телефоны",
		"black" => "Черный лист",
		"clients" => "Клиенты"
	    );

	    $pages = json_decode($pages, true);

            foreach ($pagesSuccess as $c=>$name) {
                $result .= '<span class="label label-'.(in_array($c, $pages) !== false? "success": "danger").'">'.$name.'</span>&nbsp;';
            }

            return $result;
        }

    }

?>