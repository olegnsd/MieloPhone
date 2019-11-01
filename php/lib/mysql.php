<?php

    class sql_db {

	public $timeOut = 5;
	public $pdo = null;
	public $query;
	public $result;
	public $error;
	public $sgn;
	private $statement;
	private $statementParams;
	private $driver = 'mysql';
	private $last_id = null;
	private $defautlCharset = "utf8";
	private $debug = false;

	public function __construct($dbserver, $dbuser, $dbpass, $dbname) {
	    $this->set_connect($dbserver, $dbuser, $dbpass, $dbname);
	}
	
	public function sql_db ($dbserver, $dbuser, $dbpass, $dbname) {
	    $this->set_connect($dbserver, $dbuser, $dbpass, $dbname);
	}

	public function set_connect( $dbServer, $dbUser, $dbPassword, $dbname) {
	    for( $i = 0; $i <= $this->timeOut; $i++ ) {
		$dns = $this->driver . ':host=' . $dbServer . ';dbname=' . $dbname;

		try {
		    $this->pdo = new PDO( $dns, $dbUser, $dbPassword );
		    $this->select_charset();
		    break;
		} catch( PDOException $e ) {
		    $this->e = $e;
		    echo $e;exit;
		    $i++;
		    sleep( 1 ); //интервал подключений.
		}
	    }

	    return $this;
	}

	public function get_pdo() {
	    if( !is_null( $this->pdo ) ) {
		return $this->pdo;
	    }

	    return false;
	}

	public function select_charset() {
	    $this->pdo->prepare( 'SET NAMES :chrst' )->execute( array( ':chrst' => $this->defautlCharset ) );

	    return $this;
	}

	public function set_disconnect() {
	    $this->pdo = NULL;
	    return $this;
	}

	public function set_query( $query ) {
	    $this->query = $query;
	    $this->setStatement( $query );
	    return $this;
	}

	public function setStatement( $query ) {
	    $this->statement = $this->pdo->prepare( $query );
	    return $this;
	}

	public function replaceStatement( PDOStatement $obj ) {
	    $this->statement = $obj;
	    return $this;
	}

	public function executeStatement( $params = array( ) ) {
	    $this->query = $this->statement->queryString;
	    $this->statementParams = $params;
	    $this->statement->execute( $params );
	    return $this;
	}

	public function set_bind( $parameter, $value, $data_type = PDO::PARAM_STR ) {
	    if( get_class( $this->statement ) ) {
		$this->statement->bindValue( $parameter, $value, $data_type );
		$this->set_error();
	    }
	    return $this;
	}

	public function get_bind( $parameter, $value, $length = null ) {
	    if( get_class( $this->statement ) ) {
		$this->statement->bindParam( $parameter, $value, PDO::PARAM_INPUT_OUTPUT, $length );
		$this->set_error();
	    }
	    return $this;
	}

	public function get_query() {
	    return $this->query;
	}

	public function getStatement() {
	    return $this->statement;
	}

	public function setStatementParams( $params ) {
	    $this->statementParams = $params;
	    return $this;
	}

	public function getStatementParams() {
	    return $this->statementParams;
	}

	public function set_last_id() {
	    if( $this->is_pdo( $this->pdo ) ) {
		$this->last_id = $this->pdo->lastInsertId();
	    } else {
		$this->last_id = null;
	    }

	    return $this;
	}

	public function is_pdo(
	$obj = null //uyrutyruyturyt
	) {
	    if( is_null( $this->pdo ) ) {
		return false;
	    }

	    if( get_class( $obj ) == 'PDO' ) {
		return true;
	    }

	    return false;
	}

	public function get_delete_count() {
	    return (get_class( $this->statement ) == 'PDOStatement' ? $this->statement->rowCount() : NULL);
	}

	public function get_last_id() {
	    return $this->last_id;
	}

	public function set_assoc( $pre = null ) {
	    $this->set_last_id();
	    $this->set_error();
	    unset( $this->result );
	    if( is_null( $pre ) ) {
		$pre = $this->statement;
	    }

	    if( get_class( $pre ) == 'PDOStatement' ) {
		$this->statement = $pre;
		$this->statement->execute();
		$this->set_result( $this->statement );
	    }

	    return $this;
	}

	public function get_assoc() {
	    if( isset( $this->result ) && isset( $this->result[0] ) ) {
		return $this->result;
	    }
	}

	public function execute( $query ) {
	    $this->set_query( $query );

	    if( get_class( $this->statement ) == 'PDOStatement' ) {
		return $this->set_assoc( $this->statement )->get_assoc();
	    } else {
		return null;
	    }
	}

	public function execute_nobuf( $query, $res_file = null ) {
	    if( is_null( $res_file ) ) {
		return $this->execute( $query );
	    }

	    $this->set_query( $query );

	    if( get_class( $this->statement ) == 'PDOStatement' ) {
		return $this->set_assoc2file( $this->statement, $res_file );
	    } else {
		return null;
	    }
	}

	public function set_nobuf( $res_file = null ) {
	    if( is_null( $res_file ) ) {
		if( get_class( $this->statement ) == 'PDOStatement' ) {
		    $this->pdo->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false );
		    return $this;
		} else {
		    return $this;
		}
	    }
	    if( get_class( $this->statement ) == 'PDOStatement' ) {
		return $this->set_assoc2file( $this->statement, $res_file );
	    } else {
		return $this;
	    }
	}

	public function set_error() {
	    if( !$this->statement || $this->statement && get_class( $this->statement ) != 'PDOStatement' ) {
		$res = $this->pdo->errorInfo();
		$result['message'] = $res[2];
		$result['code'] = $res[0];
	    } else {
		$res = $this->statement->errorInfo();
		$result['message'] = $res[2];
		$result['code'] = $res[0];
	    }

	    if( $result['code'] && $result['message'] ) {
		$this->error[] = $result['code'];
		$this->error[] .= ' ' . $result['message'];
		$this->error[] .= '<br/> SQL query: <pre>' . $this->get_query() . '</pre>';
	    }
	    if( $result['code'] && $result['message'] && $this->debug ) {
		echo $result['code'] . ' - ' . $result['message'] . "\n";
		print_r( $this->lastQuery() );
		die();
	    }

	    return $this;
	}

	public function get_error() {
	    return $this->error;
	}

	public function __destruct() {
	    $this->set_disconnect();

	    if( count( $this->get_error() ) > 0 ) {
		echo '<pre>';
		var_dump( $this->get_error() );
		echo '</pre>';
	    }
	}

	public function reset_error() {
	    $this->error = null;
	}

	public function mysql_escape_array( & $data ) {
	    foreach( $data as $key => $val ) {
		$data[$key] = trim( $this->pdo->quote( $val ), "'" );
	    }
	}

	function escape ($text) {
	    return mysql_escape_string ($text);
	}

    
	/**
	 * @sql str
	 * @params arr
	 * 	type 1 array(':paramName'=> 'value')
	 *  type 2 array('paramName'=> 'value')
	 * 	type 3 array(array(':paramName', 'value', PDO::TYPE))
	 * 	type 4 array(array('paramName', 'value', PDO::TYPE))
	 */
	public function query( $sql = "", $params = array( ) ) {
//livan
//	echo $sql."\n";
//	print_r($params);
//f=fopen("/tmp/log.txt","aw");
//puts($f,$sql." ".print_r($params,true));
//fclose($f);
//	echo "\n====\n\n\n";
	    $this->set_query( $sql );
	    $this->setStatementParams( $params );

	    $this->statement = $this->pdo->prepare( $this->get_query() );
	    if( is_array( $params ) && sizeof( $params ) && is_array( reset( $params ) ) ) {
		foreach( $params as $value ) {
		    $this->statement->bindValue( $value[0], $value[1], isset( $value[2] ) ? $value[2] : null  );
		}
		try {
		    $this->statement->execute();

		    $this->set_last_id();
		} catch( PDOException $e ) {
		    $this->logExp( $e );
		}
	    } else {
		try {
			
		    $this->statement->execute( $params );
		    $this->set_last_id();
		} catch( PDOException $e ) {
		    $this->logExp( $e );
		}
	    }
	    $this->set_error();
	    return $this;
	}

	public function affectedRows() {
	    return $this->statement->rowCount();
	}

	public function resultRow() {
	    return $this->statement->fetch( PDO::FETCH_ASSOC );
	}

	public function resultArray() {
	    return $this->statement->fetchAll( PDO::FETCH_ASSOC );
	}

	public function resultObject() {
	    return $this->statement->fetchObject();
	}

	public function arrayToBind( $arr, $params = array( ), $prefix = ':par_' ) {
	    $result = array(
		'str' => '',
		'params' => $params
	    );
	    foreach( $arr as $k => $val ) {
		$result['str'] .= " {$prefix}{$k},";
		$result['params'][] = array( "{$prefix}{$k}", $val, PDO::PARAM_STR );
	    }
	    $result['str'] = trim( $result['str'], ',' );
	    return $result;
	}

	public function lastQuery( $return = true ) {
	    if( $return ) {
		return array( 'sql' => $this->query, 'params' => $this->statementParams );
	    } else {
		$this->statement->debugDumpParams();
	    }
	}

	public function set_assoc2file(
	$pre, $res_file = null
	) {
	    if( is_null( $res_file ) ) {
		return false;
	    }

	    $pre->execute();
	    $this->set_last_id();
	    $this->set_error();

	    $this->set_result( $pre, $res_file );

	    return $this;
	}

	public function set_result( $pre, $res_file = false ) {
	    while( $row = $pre->fetch( PDO::FETCH_ASSOC ) ) {
		if( $res_file !== false ) {
		    $res_file->write( implode( "\t", $row ) );
		}
		$this->result[] = $row;
	    }
	}

	public function logExp( PDOException $e ) {
	    openlog( 'sql_dbPDO', LOG_PID, LOG_LOCAL5 );
	    syslog( LOG_CRIT, $e->getMessage() );
	    closelog();
	}


	/* OLD */

	public function sql_close () {
	    $this->pdo = null;
	}

	public function sql_query ($query) {
	    return $this->query($query);
	}

	public function sql_fetchrowset () {
	    return $this->resultArray();
	}

	public function sql_fetchrow () {
	    return $this->resultRow();
	}

	public function sql_nextid () {
	    $this->set_last_id();
	    return $this->last_id;
	}

	public function sql_error () {
	    return $this->get_error();
	}



    }

?>
