<?php
    class controller {
        private $hours = array (
                "-1" => "Калининград",
                "+0" => "Москва",
#               "+1" => "Самара",
                "+2" => "Екатеринбург",
                "+3" => "Омск",
                "+4" => "Красноярск",
                "+5" => "Иркутск",
                "+6" => "Якутск",
                "+7" => "Владивосток",
                "+8" => "Республика Саха",
                "+9" => "Камчатка",
        );

	private $query;
	// construncor
	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    $this->query = new query_controller();
	}
	    
	public function def() {
	    $result = "";

/*
	    if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "stats" && isset($_SESSION['PARAMS'][2]) && is_numeric($_SESSION['PARAMS'][2])) {
                $edit = $this->query->edit($_SESSION['PARAMS'][2]);

                if (!(!empty($edit) && count($edit)>0))
                    header("Location: /caller");

		if (isset($_POST['daterange'])) {
		    list($datefrom, $dateto) = explode(" - ", $_POST['daterange']);
		    $datefrom = date("Y-m-d H:i:s", strtotime($datefrom));
		    $dateto = date("Y-m-d H:i:s", strtotime($dateto));
		}
		else {
		    $datefrom = date("Y-m-d H:i:s", strtotime("now -1day"));
		    $dateto = date("Y-m-d H:i:s");
		}

		$temp = $this->query->getRings($edit['mark'], $datefrom, $dateto);

		foreach ($temp as $t) 
		    $rings[$t['date']] = ["count" => $t['total']];
		$temp = $this->query->getActiveRings($edit['mark'], $datefrom, $dateto);
		foreach ($temp as $t) 
		    $rings[$t['date']]["active"] = $t['total'];
		$temp = $this->query->getSuccessRings($edit['mark'], $datefrom, $dateto);
		foreach ($temp as $t) 
		    $rings[$t['date']]["success"] = $t['total'];
		$temp = $this->query->getBlackRings($edit['mark'], $datefrom, $dateto);
		foreach ($temp as $t) 
		    $rings[$t['date']]["black"] = $t['total'];

		$keys = array_keys($rings);

		$allRings = ["count" => [], "active" => [], "success" => [], "black" => []];

		if (count($keys) > 0 && !empty($keys)) {
#		    list($year, $month, $day, $hour) = explode("-", $keys[0]);
		    list($y, $m, $d, $h) = explode("-", date("Y-m-d-H", strtotime($datefrom))); 
		    $minDate = "{$y}, ".($m-1).", {$d}, {$h}, 59, 59";
#"{$year}, ".($month-1).", {$day}, {$hour}, 59, 59";

		    $date = date("Y-m-d H:00:00", strtotime($datefrom));
#"{$year}-{$month}-{$day} {$hour}:00:00";

		    do {
			$d = date("Y-m-d-H", strtotime("{$date}"));
		        if (!isset($rings[$d])) {
		    	    $allRings["count"][] = 0;
			    $allRings["active"][] = 0;
			    $allRings["success"][] = 0;
			    $allRings["black"][] = 0;
			}
			else {
			    $allRings["count"][] = (isset($rings[$d]["count"])? $rings[$d]["count"]: 0);
			    $allRings["active"][] = (isset($rings[$d]["active"])? $rings[$d]["active"]: 0);;
			    $allRings["success"][] = (isset($rings[$d]["success"])? $rings[$d]["success"]: 0);
			    $allRings["black"][] = (isset($rings[$d]["black"])? $rings[$d]["black"]: 0);
			}

			$date = date("Y-m-d H:i:s", strtotime("{$date} +1hour"));
		    } while ($date < $dateto);
		}
		else
		    $minDate = date("Y, m, d, H, i, s");

		$temp = $this->query->getActivesCaller($edit['id'], $datefrom, $dateto);
		$tempActives = [];
		foreach ($temp as $t)
		    $tempActives[$t['datetime']] = $t['state'];

		$date = $datefrom;

		$actives = [];
		$lastState = 0;
		do {
		    $d = date("Y-m-d-H-i", strtotime("{$date}"));

		    list($y,$m,$d2,$h,$i,$s) = explode(",", date("Y,m,d,H,i,s", strtotime($date)));
		    $m--;

		    if (!isset($tempActives[$d])) {
		    	    $actives[] = "[Date.UTC($y,$m,$d2,$h,$i,$s),{$lastState}]";
		    }
		    else {
		    	    $actives[] = "[Date.UTC($y,$m,$d2,$h,$i,$s),{$tempActives[$d]}]";
			    $lastState = $tempActives[$d];
    		    }

		    $date = date("Y-m-d H:i:s", strtotime("{$date} +1minute"));
		} while ($date < $dateto);

		$temp = $this->query->getBalanceCaller($edit['id'], $datefrom, $dateto);
		$tempBalance = [];
		foreach ($temp as $t)
		    $tempBalance[$t['datetime']] = $t['balance'];

		$date = $datefrom;

		$balance = [];
		$lastState = 0;
		do {
		    $d = date("Y-m-d-H-i", strtotime("{$date}"));

		    list($y,$m,$d2,$h,$i,$s) = explode(",", date("Y,m,d,H,i,s", strtotime($date)));
		    $m--;

		    if (!isset($tempBalance[$d])) {
#		    	    $balance[] = "[Date.UTC($y,$m,$d2,$h,$i,$s),{$lastState}]";
		    }
		    else {
		    	    $balance[] = "[Date.UTC($y,$m,$d2,$h,$i,$s),{$tempBalance[$d]}]";
			    $lastState = $tempBalance[$d];
    		    }

		    $date = date("Y-m-d H:i:s", strtotime("{$date} +1minute"));
		} while ($date < $dateto);

                $pattern        = new pattern('caller/stats');
                $pattern->set_var("RINGS", implode(", ", $allRings["count"]));
                $pattern->set_var("ACTIVE", implode(", ", $allRings["active"]));
                $pattern->set_var("SUCCESS", implode(", ", $allRings["success"]));
                $pattern->set_var("BLACK", implode(", ", $allRings["black"]));
                $pattern->set_var("NAME", $edit['name']);
                $pattern->set_var("DATESTART", $minDate);
                $pattern->set_var("DATEFROM", date("d.m.Y H:i:s", strtotime($datefrom)));
                $pattern->set_var("DATETO", date("d.m.Y H:i:s", strtotime($dateto)));
                $pattern->set_var("DATESTART_ACTIVE", date("Y, m, d, H, i, s", strtotime($datefrom)));
                $pattern->set_var("ACTIVES", implode(", ", $actives));
                $pattern->set_var("BALANCE", implode(", ", $balance));
                $result .= $pattern->result();

	    }
	    else
*/
	    if (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "edit" && isset($_SESSION['PARAMS'][2]) && is_numeric($_SESSION['PARAMS'][2])) {
                $user = $this->query->edit($_SESSION['PARAMS'][2]);

                if (!(!empty($user) && count($user)>0))
                    header("Location: /caller");

                $pattern        = new pattern('caller/edit');
                $pattern->set_var("NAME", $user['name']);
                $pattern->set_var("PHONE", $user['phone']);
                $pattern->set_var("EMAIL", $user['email']);
                $pattern->set_var("SENDNO", (int) $user['send'] == 0? "CHECKED": "");
                $pattern->set_var("SENDSMS", (int) $user['send'] == 1? "CHECKED": "");
                $pattern->set_var("SENDEMAIL", (int) $user['send'] == 2? "CHECKED": "");
                $pattern->set_var("MARK", $user['mark']);
                $pattern->set_var("ID", $user['id']);
                $hoursText = "";
                foreach ($this->hours as $h=>$nameHour)
                        $hoursText .= '<option value="'.$h.'" '.($user['timezone'] == $h? "SELECTED": "").'>'.$nameHour." ({$h}:00)".'</option>';
                $pattern->set_var("HOURS", $hoursText);
                $result .= $pattern->result();

	    }
            elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "add") {
                $pattern        = new pattern('caller/add');
                $hoursText = "";
                foreach ($this->hours as $h=>$nameHour)
                        $hoursText .= '<option value="'.$h.'" '.($user['timezone'] == $h? "SELECTED": "").'>'.$nameHour." ({$h}:00)".'</option>';
                $pattern->set_var("HOURS", $hoursText);
                $result .= $pattern->result();
            }
	    elseif (isset($_SESSION['PARAMS'][1]) && $_SESSION['PARAMS'][1] == "save") {
		if (!isset($_POST['id'])) {
		    $sound 		= date("YmdHis");
            	    $pattern        = new pattern('caller/newcaller');
		    $pattern->set_var("MARK", $_POST['mark']);
		    $pattern->set_var("SOUND", $sound);
		    $result = $pattern->result();

		    $_POST['sound'] = $sound;
//		    file_put_contents($_SESSION['CONF']['FILE']['ASTCONF'], $result, FILE_APPEND);
		}

		$this->query->save($_POST);
		header("Location: /caller");
	    }
	    else {	    
		$pattern	= new pattern('caller/list_header');
		$result	.= $pattern->result();

		$list = $this->query->getList();
		
		if (!empty($list) && count($list)>0) {
		    $num = 0;
		    foreach ($list as $c) {
			$pattern	= new pattern('caller/list_row');
			$pattern->set_var("NAME", $c['name']);
			$pattern->set_var("MARK", $c['mark']);
			$pattern->set_var("PHONE", $c['phone']);
//			$pattern->set_var("BUTTONS", '');
			$pattern->set_var("BUTTONS", '
<a href="/caller/edit/'.$c['id'].'" class="btn btn-xs btn-default" title="Изменить параметры телефона"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>&nbsp;
<a href="/stats/stats/'.$c['id'].'" class="btn btn-xs btn-info" title="Статистика по телефонам"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>&nbsp;
');
			$result	.= $pattern->result();			
		    }
		}
		
		$pattern	= new pattern('caller/list_footer');
		$result	.= $pattern->result();
	    }

	    return $result;
	}

    }

?>