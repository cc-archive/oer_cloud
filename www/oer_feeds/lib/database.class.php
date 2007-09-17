<?php
		
# include the ADODB library for abstracting the underlying database
require(ADODBDIR . "/adodb.inc.php");

# define a class for connecting to, extracting and modifying data from a database

class Database {

	# predefine various variables
	protected $_dbType			= "mysql";
	public $_rows;			# holder for multiple records
	public $_row;			# holder for a single record
	public $_result;
	public $_rowCount;
	public $_fieldCount;
	public $_affectedRows;
	public $_insertId;
	public $_error;
	public $_dbConn;

	# class constructor
	function Database() {

		# connect to the database
		$this->Connect(
			DBHOST,
			DBUSER,
			DBPASS,
			DBNAME
		);

	}

	##------------------------------------------------------------------##

	# connect to the database
	function Connect($dbHost, $dbUser, $dbPass, $dbName) {
		$this->_dbConn = &ADONewConnection($this->_dbType);
		if	(
				$this->_dbConn->Connect
					(
						$dbHost,
						$dbUser,
						$dbPass,
						$dbName
					)
			)
		{
			return true;
		} else {
			# grab error if the connection fails
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError();
			}
			return false;
		}

	}
	
	##------------------------------------------------------------------##

	# close the connection to the database
	function Close() {

		if ( isset($this->_dbConn) ) {
			if ( $this->_dbConn->Close() ) {
				return true;
			} else {
				# grab error if the connection fails
				$this->_error = $this->_dbConn->ErrorMsg();
				if ( DBDEBUG == "true" ) {
					$this->PrintError();
				}
				return false;
			}
		}

	}

	##------------------------------------------------------------------##

	# handles select queries where multiple rows are expected
	function Select($sql) {

		$this->_result = $this->_dbConn->Execute($sql);

		if ( $this->_result ) {
			$this->_rowCount = $this->_result->RecordCount();
			$this->_fieldCount = $this->_result->FieldCount();
			$this->_rows = $this->_result->GetRows();
			return true;
		} else {
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles select queries where only one record is expected
	function SelectOne($sql) {

		$this->_result = $this->_dbConn->Execute($sql);

		if ( $this->_result ) {
			$this->_fieldCount = $this->_result->FieldCount();
			$this->_rowCount = $this->_result->RecordCount();
			$this->_row = $this->_result->FetchRow();
			return true;
		} else {
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles select queries that need to return a restricted record set
	function SelectLimit($sql, $rows, $offset) {

		$this->_result = $this->_dbConn->SelectLimit($sql, $rows, $offset);

		if ( $this->_result ) {
			$this->_rowCount = $this->_result->RecordCount();
			$this->_fieldCount = $this->_result->FieldCount();
			$this->_rows = $this->_result->GetRows();
			return true;
		} else {
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# handles queries that will alter data
	function Modify($sql) {
		
		$this->_result = $this->_dbConn->Execute($sql);

		if ( $this->_result ) {
			$this->_affectedRows = $this->_dbConn->Affected_Rows();
			return true;
		} else {
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError($sql);
			}
			return false;
		}
	
	}

	##------------------------------------------------------------------##

	# get auto_incremented ID of last insert statement
	function InsertId() {

		$this->_result = $this->_dbConn->Insert_ID();

		if ( $this->_result ) {
			$this->_insertId = $this->_result;
			return $this->_insertId;
		} else {
			$this->_error = $this->_dbConn->ErrorMsg();
			if ( DBDEBUG == "true" ) {
				$this->PrintError();
			}
			return false;
		}

	}

	##------------------------------------------------------------------##

	# clean up and escape strings to be inserted into database
	function EscapeString($string) {

		$string = trim($string);

		if ( ! is_numeric($string) ) {
			$string = $this->_dbConn->qstr( $string, get_magic_quotes_gpc() );
		}

		# the ADODB function above seems to add single quotes around the
		# submitted string.  i like to add those myself at the time of
		# the query, so strip them off here
		$string = trim($string, "'");

		return $string;

	}

	##------------------------------------------------------------------##

	# print an error to the screen and then exit the script
	function PrintError($sql = "") {

		$thisScript = basename($_SERVER['PHP_SELF']);
		echo <<<HTML
<html>
<head>
	<title>Database Error</title>
</head>
<body>
	<div>
		<p>There was a database error.</p>
		<p><strong>Script</strong>: $thisScript</p>
		<p><strong>SQL</strong>: $sql</p>
		<p><strong>Error</strong>: <span style='color: red;'>$this->_error</span></p>
	</div>
</body>
</html>

HTML;

		exit;

		return true;

	}

	##------------------------------------------------------------------##

}
