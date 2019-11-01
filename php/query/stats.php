<?php
    class query_controller extends sql_db  {
	function query_controller() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}

	function getCallers($aa) {
		$sql = "select * from callers where mark in ('".implode("','", $_SESSION['AUTH']['caller'])."') order by name asc";
#echo $sql;
		return $this->query( $sql, $param )->resultArray();
	}

        function currentTask() {
            $sql = "select t.*, u.timezone from tasks t, callers u where t.state = :state and t.caller = u.mark and mark in ('".implode("','", $_SESSION['AUTH']['caller'])."') order by t.dateadd asc";
            $param[] = array(':state', 'current', PDO::PARAM_STR);
            return $this->query( $sql, $param )->resultArray();
        }


	function getNewResponse($id) {
		$sql = "select count(*) as total from tasks_base_info i
			  right join (
			    select * from tasks_base where tid = :id and press = 'Y' and send = 'Y' order by datering
			  ) as b on (b.id = i.pid)
			where
			  if(isnull(i.id), 0, 1) = 0";
		$param[] = array(':id', $id, PDO::PARAM_INT);
		return $this->query( $sql, $param )->resultRow();
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

        function edit($id) {
            $sql = "select * from callers where id = :id";
            $param[] = array(':id', $id, PDO::PARAM_INT);
            return $this->query( $sql, $param )->resultRow();
        }


    }

?>