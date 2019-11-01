<?php
    class auth extends sql_db {
	function __construct() {
	    parent::sql_db($_SESSION['CONF']['DB']['HOST'], $_SESSION['CONF']['DB']['USER'], $_SESSION['CONF']['DB']['PASS'], $_SESSION['CONF']['DB']['NAME']);
	}
	
/*
	function login ($login, $password) {
	    $sql = "select * from users where user='".$this->escape($login)."' and pass='".$this->escape($password)."'";
	    $this->sql_query($sql);
	    return $this->sql_fetchrow();
	}
*/

	function auth ($login, $code) {
            $sql = "select * from users where user=:login";
            $param[] = array(':login', $login, PDO::PARAM_STR);
            $user = $this->query( $sql, $param )->resultRow();
            if(password_verify($code, $user['code'])){
                $auth = $user;
            }else{
                $auth=FALSE;
            }
            return $auth;
	}

	function getPhone ($login) {
            $sql = "select phone from users where user=:login";
            $param[] = array(':login', $login, PDO::PARAM_STR);
            $temp = $this->query( $sql, $param )->resultRow();
	    return (!empty($temp) && count($temp) > 0? $temp['phone']: false);
	}

	function updateCode($login, $code) {
            $sql = "update users set code = :code where user=:login";
            $param[] = array(':login', $login, PDO::PARAM_STR);
            $param[] = array(':code', password_hash($code, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $this->query( $sql, $param );
	}

	function setRemember($id, $remember) {
            $sql = "update users set remember = :remember where id=:id";
            $param[] = array(':id', $id, PDO::PARAM_INT);
            $param[] = array(':remember', $remember, PDO::PARAM_STR);
            $this->query( $sql, $param );
	}

	function getRemember ($remember) {
            $sql = "select * from users where remember=:remember";
            $param[] = array(':remember', $remember, PDO::PARAM_STR);
            $temp = $this->query( $sql, $param )->resultRow();
	    return (!empty($temp) && count($temp) > 0? $temp: false);
	}


    }

?>
