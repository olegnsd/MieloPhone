<?php
    class controller {
	private $query;
	// construncor
	function controller() {
	    include $_SESSION['CONF']['DIRS']['QUERY'].basename (__FILE__);
	    $this->query = new query_controller();
	}

	public function def() {
	    $result = "";

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
#                   list($year, $month, $day, $hour) = explode("-", $keys[0]);
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
#                           $balance[] = "[Date.UTC($y,$m,$d2,$h,$i,$s),{$lastState}]";
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

	    else {
                $pattern        = new pattern('stats/stats');

		$rowset = $this->getCallers();
		$callersText = "";

		foreach ($rowset as $row) {
 	               	$pattern2        = new pattern('stats/active');
			$pattern2->set_var("NAME", $row['name']." ({$row['mark']}), {$row['ip']}, {$row['currentstate']}");
			$pattern2->set_var("BALANCE", $this->viewBalance($row['balance'], $row['id']));
			$pattern2->set_var("LASTUPDATE", date("d.m.Y H:i:s", strtotime($row['updateactive'])));
			$pattern2->set_var("LASTCALL", !is_null($row['last_call'])? date("d.m.Y H:i:s", strtotime($row['last_call'])): "");
			$pattern2->set_var("STATUS", $this->viewStatus($row['active']));
        	      	$callersText .= $pattern2->result();
		}

		$pattern->set_var("CALLERS", $callersText);

		$tasks = $this->query->currentTask();
		$tasksText = "";
		if (!empty($tasks) && count($tasks) > 0) {
	               	$pattern2        = new pattern('stats/task');
			$pattern2->set_var("CALLER", "<strong>Канал</strong>");
			$pattern2->set_var("PRESS", "<strong>Отклики</strong>");
			$pattern2->set_var("PROGRESS", "<strong>Прогресс обзвона</strong>");
			$pattern2->set_var("PARAMS", "<strong>Параметры</strong>");
        	      	$tasksText .= $pattern2->result();

			foreach ($tasks as $t) {
				$timeoff = date("Hi", strtotime("NOW {$t['timezone']}hour")) < str_replace(":", "", $t['timefrom']) || date("Hi", strtotime("NOW {$t['timezone']}hour")) > str_replace(":", "", $t['timeto'])? true: false;

 	               		$pattern2        = new pattern('stats/task');
				$pattern2->set_var("CALLER", $this->_viewChannel($t['caller'], $rowset));
				$pattern2->set_var("PRESS", $this->_viewCountPress($t['id'], $t['press']));
				$pattern2->set_var("PROGRESS", $this->_viewStatus($t['total'], $t['send'], $timeoff, $t['comment']));
				$pattern2->set_var("PARAMS", $this->_viewParams($t['sms_enable'], $t['email_enable']));
        	      		$tasksText .= $pattern2->result();
			}
		}
		else
               		$tasksText = "Нет запущенных процессов";

		$pattern->set_var("TASKS", $tasksText);	

                $result .= $pattern->result();
	    }

	    return $result;
	}

	function getCallers ($callers = false) {
		$temp = array();
		$rowset = $this->query->getCallers("mark = '".implode("' or mark = '", $callers)."'");
		foreach ($rowset as $row)
			$temp[$row['mark']] = $row;
		return $temp;
	}

	function viewStatus ($status) {
		switch ($status) {
			case "yes": return '<span class="label label-success">Включено</span>';
			case "no": return '<span class="label label-danger">Выключен</span>';
			default: return '<span class="label label-warning">Неизвестно</span>';
		}
		return $status;
	}

	function viewBalance ($balance, $id) {
		$a1 = "<a href='/stats/stats/{$id}'>";
		$a2 = "</a>";
		if ($balance > 0)
			return $a1.'<span class="label label-success">'.$balance.'<span class="glyphicon glyphicon-rub" aria-hidden="true"></span></span>'.$a2;
		else if ($balance < 0)
			return $a1.'<span class="label label-danger">'.($balance + 0).'<span class="glyphicon glyphicon-rub" aria-hidden="true"></span></span>'.$a2;
		return '<span class="label label-danger">Неизвестно</span>';
	}

        function _viewChannel($channel, $callers) {
            $result = "";

            foreach ($callers as $caller) {
                if (in_array($caller['mark'], explode(",", $channel)) !== false)
                    $result .= '<span class="label label-success">'.$caller['name'].'</span><br/>';
            }

            return $result;
        }

        function _viewCountPress ($id, $count) {
		if ($count == 0)
			return '<span class="label label-default"><span class="glyphicon glyphicon-bullhorn" aria-hidden="true" title="Информация о привлечении"></span>&nbsp;&nbsp;0</span>';
		else {
			$new = $this->query->getNewResponse($id);
			return '<a href="/task/response/'.$id.'/interest" style="text-decoration: none" title="Показать отклики'.($new['total'] > 0? ". Есть новые отклики":"").'"><span class="label label-success"><span class="glyphicon glyphicon-bullhorn" aria-hidden="true" title="Информация о привлечении"></span>&nbsp;&nbsp;'.$count.($new['total'] > 0? "<sup class='blink'>+".$new['total']."</sup>": "").'</span></a>';
		}
        }

        function _viewParams ($sms, $email) {
		$result = "";

		if ($sms == 0)
			$result .= '<span class="label label-danger">SMS</span> ';
		else
			$result .= '<span class="label label-success">SMS</span> ';

		if ($email == 0)
			$result .= '<span class="label label-danger">Email</span>';
		else
			$result .= '<span class="label label-success">Email</span>';

		return $result;
        }

        function _viewStatus($total, $send, $timeoff, $comment) {
            $result = "";
            if (is_null($total)) {
                $result .= '<span class="label label-default">База не загружена</span>';
            }
            else {
                $result .= '<div class="progress" style="margin-bottom: 0px">'."\n";

                $result .= '<div class="progress-bar active progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'.sprintf("%.2f", ($total == 0? 0: $send/$total*100)).'" aria-valuemin="0" aria-valuemax="100" style="width: '.sprintf("%.0f", ($total == 0? 0: $send/$total*100)).'%">'."\n";
		if ($send/$total*100 > 40)
               		$result .= sprintf("%.2f", ($total == 0? 0: $send/$total*100)).'%'.($timeoff? " <strong><span class='glyphicon glyphicon-off' aria-hidden='true'></span> Не активно</strong>": " <strong><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Процесс активен</strong>".($comment != ""? " &mdash;".$comment: ""))."\n";
                $result .= '</div>'."\n";
                $result .= '<div class="progress-bar progress-bar-'.($timeoff? "danger": "dis").'" role="progressbar" aria-valuenow="'.sprintf("%.2f", ($total == 0? 0: $send/$total*100)).' aria-valuemin="0" aria-valuemax="100" style="width: '.sprintf("%.0f", ($total == 0? 0: ($total-$send)/$total*100)).'%">'."\n";
		if ($send/$total*100 <= 40)
                	$result .= sprintf("%.2f", ($total == 0? 0: $send/$total*100)).'%'.($timeoff? " <strong><span class='glyphicon glyphicon-off' aria-hidden='true'></span> Не активно</strong>": " <strong><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Процесс активен</strong>".($comment != ""? " &mdash;".$comment: ""))."\n";
                $result .= '</div>'."\n";


                $result .= '</div>'."\n";
            }
            return $result;
        }


    }

?>