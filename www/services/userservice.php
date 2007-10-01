<?php
class UserService {
    var $db;

    function &getInstance(&$db) {
        static $instance;
        if (!isset($instance))
            $instance =& new UserService($db);
        return $instance;
    }

    var $fields = array(
        'primary'   =>  'uId',
        'username'  =>  'username',
        'password'  =>  'password'
    );
    var $profileurl;
    var $tablename;
    var $sessionkey;
    var $cookiekey;
    var $cookietime = 1209600; // 2 weeks

    function UserService(&$db) {
        $this->db =& $db;
        $this->tablename = $GLOBALS['tableprefix'] .'users';
        $this->sessionkey = $GLOBALS['cookieprefix'] .'-currentuserid';
        $this->cookiekey = $GLOBALS['cookieprefix'] .'-login';
        $this->profileurl = createURL('profile', '%2$s');
    }

    function _checkdns($host) {
        if (function_exists('checkdnsrr')) {
            return checkdnsrr($host);
        } else {
            return $this->_checkdnsrr($host);
        }
    }

    function _checkdnsrr($host, $type = "MX") {
        if(!empty($host)) {
            @exec("nslookup -type=$type $host", $output);
            while(list($k, $line) = each($output)) {
                if(eregi("^$host", $line)) {
                    return true;
                }
            }
            return false;
        }
    }

    function _getuser($fieldname, $value) {
        $query = 'SELECT * FROM '. $this->getTableName() .' WHERE '. $fieldname .' = "'. $this->db->sql_escape($value) .'"';

        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        if ($row =& $this->db->sql_fetchrow($dbresult))
            return $row;
        else
            return false;
    }

    function _in_regex_array($value, $array) {
        foreach ($array as $key => $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    function _randompassword() {
    	# No need to seed the rand() and mt_rand() functions since v4.2.0
	# and on top of that type casting an alphanumeric md5 hash as an 
	# integer won't work and PHP was frequently returning the same seed,
	# namely , '0', so the passwords weren't random anymore.
	# http://us.php.net/manual/en/function.mt-srand.php, 
        #$seed = (integer) md5(microtime());
        #mt_srand($seed);
        $password = mt_rand(1, 99999999);
        $password = substr(md5($password), mt_rand(0, 19), mt_rand(6, 12));
        return $password;
    }

    function _updateuser($uId, $fieldname, $value) {
        $updates = array ($fieldname => $value);
        $sql = 'UPDATE '. $this->getTableName() .' SET '. $this->db->sql_build_array('UPDATE', $updates) .' WHERE '. $this->getFieldName('primary') .'='. intval($uId);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function block($user) {
        if (!is_numeric($user)) {
            $userinfo   = $this->getUserByUsername($user);
            $user       = $userinfo[$this->getFieldName('primary')];
        }

        $uid            = $this->getCurrentUserId();
        $datetime       = gmdate('Y-m-d H:i:s', time());

        $values = array(
            'uId'       => $uid,
            'item'      => $user,
            'score'     => -1,
            'sDatetime' => $datetime,
            'sModified' => $datetime
        );
        $sql    = 'INSERT INTO '. $GLOBALS['tableprefix'] .'scores '. $this->db->sql_build_array('INSERT', $values);
        if (!($dbresult =& $this->db->sql_query($sql))){
            message_die(GENERAL_ERROR, 'userservice: block', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        } else {
            return true;
        }
    }

    function getProfileUrl($id, $username) {
        return sprintf($this->profileurl, urlencode($id), urlencode($username));
    }

    function getUserByUsername($username) {
        return $this->_getuser($this->getFieldName('username'), $username);
    }

    function getUser($id) {
        return $this->_getuser($this->getFieldName('primary'), $id);
    }

    function isLoggedOn() {
        return ($this->getCurrentUserId() !== false);
    }

    function &getCurrentUser($refresh = FALSE, $newval = NULL) {
        static $currentuser;
        if (!is_null($newval)) //internal use only: reset currentuser
            $currentuser = $newval;
        else if ($refresh || !isset($currentuser)) {
            if ($id = $this->getCurrentUserId())
                $currentuser = $this->getUser($id);
            else
                return;
        }
        return $currentuser;
    }

    function isAdminByUID($userid) {
        if(!$userid)
            return false;
        $query = sprintf("SELECT * FROM sc_users WHERE uId = '%s'", $userid);
        if (! ($dbresult =& $this->db->sql_query($query)) ) {                 
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
                return false;            }   
        if ($row = $this->db->sql_fetchrow($dbresult)) {
                return $row['isAdmin'];
    }
        return false;
    }

    function isFlaggedByUID($userid) {        
        $query = sprintf("SELECT * FROM sc_users WHERE uId = '%s'", $userid);
        if (! ($dbresult =& $this->db->sql_query($query)) ) { 
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
                return false;
            }        if ($row = $this->db->sql_fetchrow($dbresult)) {
                return $row['isFlagged'];
            }        return false;
    }

    function isAdminByUsername($username) {
        $username = $this->safeString($username);
        $query = sprintf("SELECT * FROM sc_users WHERE username = '%s'", $username);        
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);                
                return false;            
            }       
        if ($row = $this->db->sql_fetchrow($dbresult)) {
                return $row['isAdmin'];
            }
        return false;
    }

    function isFlaggedByUsername($username) {
        $username = $this->safeString($username);
        $query = sprintf("SELECT * FROM sc_users WHERE username = '%s'", $username);        
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
                return false;
            }
        if ($row = $this->db->sql_fetchrow($dbresult)) {
                return $row['isFlagged'];
            }
        return false;
    }

    function safeString($string) {
        if(get_magic_quotes_gpc()){
            if(ini_get('magic_quotes_sybase')) 
                $safe = str_replace($string);
            else 
                $safe = stripslashes($string);
        }
        $more_safe = mysql_real_escape_string($safe);
        return $more_safe;
    }

    function isAdminPassDefault($adminUser) {
        $adminUser = $this->safeString($adminUser);
        $query = sprintf("SELECT password FROM sc_users WHERE username = '%s'", $adminUser);
        if(!($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not perform action', '',
                __LINE__, __FILE__, $query, $this->db);
            return false;
        }
        $row = mysql_fetch_array($dbresult);
        if($row['password'] == 'd033e22ae348aeb5660fc2140aec35850c4da997')
            return true;
        else
            return false;
    }

    function getCurrentUserId() {
        if (isset($_SESSION[$this->getSessionKey()])) {
            return $_SESSION[$this->getSessionKey()];
        } else if (isset($_COOKIE[$this->getCookieKey()])) {
            $cook = split(':', $_COOKIE[$this->getCookieKey()]);
            //cookie looks like this: 'id-md5(username+password)'
            $query = 'SELECT * FROM '. $this->getTableName() .
                     ' WHERE MD5(CONCAT('.$this->getFieldName('username') .
                                     ', '.$this->getFieldName('password') .
                     ')) = \''.$this->db->sql_escape($cook[1]).'\' AND '.
                     $this->getFieldName('primary'). ' = '. $this->db->sql_escape($cook[0]);

            if (! ($dbresult =& $this->db->sql_query($query)) ) {
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
                return false;
            }

            if ($row = $this->db->sql_fetchrow($dbresult)) {
                $_SESSION[$this->getSessionKey()] = $row[$this->getFieldName('primary')];
                return $_SESSION[$this->getSessionKey()];
            }
        }
        return false;
    }

    function login($username, $password, $remember = false, $path = '/') {
        $password = $this->sanitisePassword($password);
        $query = 'SELECT '. $this->getFieldName('primary') .' FROM '. $this->getTableName() .' WHERE '. $this->getFieldName('username') .' = "'. $this->db->sql_escape($username) .'" AND '. $this->getFieldName('password') .' = "'. $this->db->sql_escape($password) .'"';

        $result     = false;
        $message    = 'fail';

        $dbresult =& $this->db->sql_query($query);
        if ($row =& $this->db->sql_fetchrow($dbresult)) {
            if ($this->isVerified($username)) {
            $id = $_SESSION[$this->getSessionKey()] = $row[$this->getFieldName('primary')];
            if ($remember) {
                    $cookie = $id .':'. md5($username . $password);
                    setcookie($this->cookiekey, $cookie, time() + $this->cookietime, $path);
            }
                $result     = true;
                $message    = 'success';
        } else {
                $message    = 'unverified';
        }
    }

        return array(
            'result'    => $result,
            'message'   => $message
        );
    }

    function logout($path = '/') {
        @setcookie($this->cookiekey, NULL, time() - 1, $path);
        unset($_COOKIE[$this->cookiekey]);
        session_unset();
        $this->getCurrentUser(TRUE, false);
    }

   // Gets the list of user IDs being watched by the given user.
    function getWatchlist($uId) {
      $sql = 'SELECT watched FROM '. $GLOBALS['tableprefix'] .'watched WHERE uId = '. intval($uId);

      $result = $this->db->sql_query($sql);
      $watched = array();
      while ($row = $this->db->sql_fetchrow($result)) {
         $watched[] = $row['watched'];
        }
      $this->db->sql_freeresult($result);

      return $watched;
    }

    function getWatchNames($uId, $watchedby = false) {
        // Gets the list of user names being watched by the given user.
        // - If $watchedby is false get the list of users that $uId watches
        // - If $watchedby is true get the list of users that watch $uId
        if ($watchedby) {
            $table1 = 'b';
            $table2 = 'a';
        } else {
            $table1 = 'a';
            $table2 = 'b';
        }

      $sql = 'SELECT ' . $table1 .'.'. $this->getFieldName('username') .
         ' FROM ' . $this->getTableName() .' AS a, '. $this->getTableName() .' AS b, '. $GLOBALS['tableprefix'] .'watched AS w' .
         ' WHERE w.watched = a.'. $this->getFieldName('primary') .' AND w.uId = b.'. $this->getFieldName('primary') .' AND '. $table2 .'.'. $this->getFieldName('primary') .' = '. intval($uId) .
         ' ORDER BY ' . $table1 .'.'. $this->getFieldName('username');

      $result = $this->db->sql_query($sql);
      $usernames = array();
      while ($row = $this->db->sql_fetchrow($result)) {
         $usernames[] = $row['username'];
        }
      $this->db->sql_freeresult($result);

      return $usernames;
        }

    function getWatchStatus($watcheduser, $currentuser) {
      // Returns true if the current user is watching the given user, and false otherwise
      $query = 'SELECT COUNT(wId) AS watching FROM '. $GLOBALS['tableprefix'] .'watched WHERE uId = '. intval($currentuser) .' AND watched = '. intval($watcheduser);
      $this->db->sql_query($query);
      $result = $this->db->sql_fetchfield('watching') > 0 ? true : false;
      return $result;
        }

    function setWatchStatus($subjectUserID) {
        if (!is_numeric($subjectUserID))
            return false;

        $currentUserID = $this->getCurrentUserId();
        $watched = $this->getWatchStatus($subjectUserID, $currentUserID);

        if ($watched) {
            $sql = 'DELETE FROM '. $GLOBALS['tableprefix'] .'watched WHERE uId = '. intval($currentUserID) .' AND watched = '. intval($subjectUserID);
            if (!($dbresult =& $this->db->sql_query($sql))) {
                $this->db->sql_transaction('rollback');
                message_die(GENERAL_ERROR, 'Could not add user to watch list', '', __LINE__, __FILE__, $sql, $this->db);
                return false;
            }
        } else {
            $values = array(
                'uId' => intval($currentUserID),
                'watched' => intval($subjectUserID)
            ); 
            $sql = 'INSERT INTO '. $GLOBALS['tableprefix'] .'watched '. $this->db->sql_build_array('INSERT', $values);
            if (!($dbresult =& $this->db->sql_query($sql))) {
                $this->db->sql_transaction('rollback');
                message_die(GENERAL_ERROR, 'Could not add user to watch list', '', __LINE__, __FILE__, $sql, $this->db);
                return false;
            }
        }

        $this->db->sql_transaction('commit');
        return true;
    }

    function addUser($username, $name, $homepage, $password, $email) {
        // Set up the SQL UPDATE statement.
        $datetime = gmdate('Y-m-d H:i:s', time());
        $password = $this->sanitisePassword($password);
        
		$homepage = ("" == trim($homepage)) ? "" : trim($homepage);

        // Get the client's IP address and the date; note that the date is in GMT.
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else
            if (getenv('REMOTE_ADDR'))
                $ip = getenv('REMOTE_ADDR');
            else
                $ip = getenv('HTTP_X_FORWARDED_FOR');
                
        $values = array(
            'username'  => $username,
            'name'      => $name,
            'homepage'  => $homepage,
            'password'  => $password,
            'email'     => $email,
            'uDatetime' => $datetime,
            'uModified' => $datetime,
            'uIp'       => $ip
        );
        $sql = 'INSERT INTO '. $this->getTableName() .' '. $this->db->sql_build_array('INSERT', $values);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not insert user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function updateUser($uId, $password, $name, $email, $homepage, $uContent) {
        if (!is_numeric($uId))
            return false;

        // Set up the SQL UPDATE statement.
        $moddatetime = gmdate('Y-m-d H:i:s', time());
        if ($password == '')
            $updates = array ('uModified' => $moddatetime, 'name' => $name, 'email' => $email, 'homepage' => $homepage, 'uContent' => $uContent);
        else
            $updates = array ('uModified' => $moddatetime, 'password' => $this->sanitisePassword($password), 'name' => $name, 'email' => $email, 'homepage' => $homepage, 'uContent' => $uContent);
        $sql = 'UPDATE '. $this->getTableName() .' SET '. $this->db->sql_build_array('UPDATE', $updates) .' WHERE '. $this->getFieldName('primary') .'='. intval($uId);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function sanitisePassword($password) {
        return sha1(trim($password));
    }

    function generatePassword($uId) {
        if (!is_numeric($uId))
            return false;

        $password = $this->_randompassword();

        if ($this->_updateuser($uId, $this->getFieldName('password'), $this->sanitisePassword($password)))
            return $password;
        else
            return false;
    }

    function isBlockedEmail($email) {
       // Check whitelist
       $whitelist = $GLOBALS['email_whitelist'];
       if (!is_null($whitelist) && is_array($whitelist)) {
          if (!$this->_in_regex_array($email, $whitelist)) {
             // Not in whitelist -> blocked
             return true;
          }
       }

       // Check blacklist
       $blacklist = $GLOBALS['email_blacklist'];
       if (!is_null($blacklist) && is_array($blacklist)) {
          if ($this->_in_regex_array($email, $blacklist)) {
             // In blacklist -> blocked
             return true;
          }
       }

       // Not blocked
             return false;
         }


    function isReserved($username) {
        return (in_array($username, $GLOBALS['reservedusers']));
    }

    function isValidEmail($email) {
        $pattern = '/^((?:(?:(?:\w[\.\-\+_]?)*)\w)+)\@((?:(?:(?:\w[\.\-_]?){0,62})\w)+)\.(\w{2,6})$/i';
        if (preg_match($pattern, $email)) {
            list($emailUser, $emailDomain) = split("@", $email);

            // Check if the email domain has a DNS record
            if ($this->_checkdns($emailDomain)) {
                return true;
            }
        }
        return false;
    }

    function isVerified($username) {
        $userinfo = $this->getUserByUsername($username);
        return ($userinfo['uStatus'] == 1);
    }

    function setStatus($status) {
        $sql = 'UPDATE '. $this->getTableName() .' SET uStatus = '. intval($status);
        return $this->db->sql_query($sql);
    }

    function verify($username, $hash) {
        $userinfo   = $this->getUserByUsername($username);
        $datetime   =& $userinfo['uDatetime'];
        $userid     =& $userinfo[$this->getFieldName('primary')];
        $storedhash = md5($username . $datetime);
        if ($storedhash == $hash) {
            return $this->_updateuser($userid, 'uStatus', 1);
        } else {
            return false;
        }
    }

    // Properties
    function getTableName()       { return $this->tablename; }
    function setTableName($value) { $this->tablename = $value; }

    function getFieldName($field)         { return $this->fields[$field]; }
    function setFieldName($field, $value) { $this->fields[$field] = $value; }

    function getSessionKey()       { return $this->sessionkey; }
    function setSessionKey($value) { $this->sessionkey = $value; }

    function getCookieKey()       { return $this->cookiekey; }
    function setCookieKey($value) { $this->cookiekey = $value; }


	/**
	 *
	 * Functions added by Nathan Kinkade
	 *
	 */

	# Get all users that have admin privileges
	function getAllUsers($sort = "username") {

		# do some input validation on the $sort variable
		switch ( $sort ) {
			case "isAdmin":		$orderBy = "isAdmin DESC, username"; break;
			case "isFlagged":	$orderBy = "isFlagged DESC, username"; break;
			case "uStatus":		$orderBy = "uStatus, username"; break;
			case "username":	$orderBy = "username"; break;
			case "name":		$orderBy = "name, username"; break;
			case "uDatetime":	$orderBy = "uDatetime DESC, username"; break;
			case "uModified":	$orderBy = "uModified DESC, username"; break;
			case "email":		$orderBy = "email, username"; break;
			case "homepage":	$orderBy = "homepage, username"; break;
			default:			$orderBy = "username";
		}

		$sql = sprintf ("
			SELECT * FROM sc_users
			ORDER BY %s
			",
			$orderBy
		);
		$queryId = $this->db->sql_query($sql);
		$users = $this->db->sql_fetchrowset($queryId);

		return $users;

	}

	function editUser() {
		$sql = sprintf ("
			UPDATE sc_users SET
				username = '%s',
				name = '%s',
				email = '%s',
				homepage = '%s',
				uModified = '%s'
				%s
			WHERE uId = '%s'
			",
			trim($_POST['username']),
			trim($_POST['name']),
			trim($_POST['email']),
			trim($_POST['homepage']),
   			gmdate('Y-m-d H:i:s', time()),
			trim($_POST['password']) ? ", password = '" . $this->sanitisePassword($_POST['password']) . "'" : "",
			$_POST['userid']
		);
		if ( $queryId = $this->db->sql_query($sql) ) {
			$tplVars['msg'] = "The user was successfully edited";
			return true;
		} else {
			$tplVars['error'] = "There was an error.  The user may not have been edited.";
			return false;
		}
	}

	# make changes to users
	function modifyUsers() {

		global $tplVars;

		# initialize a sql string
		$sql = "";
		$updateFields = "";

		if ( isset($_POST['userList']) ) {
			# pull list of user ids from the submitted form
			$userIds = implode(",", $_POST['userList']);
			switch ( $_POST['uAction'] ) {
				case "activate":
					$updateFields = "uStatus = '1'";
					$actionMsg = "Activating the selected user(s)";
					break;
				case "deactivate":
					$updateFields = "uStatus = '0'";
					$actionMsg = "Deactivating the selected user(s)";
					break;
				case "flag":
					$updateFields = "isFlagged = '1'";
					$actionMsg = "Flagging the selected user(s)";
					break;
				case "unflag":
					$updateFields = "isFlagged = '0'";
					$actionMsg = "Unflagging the selected user(s)";
					break;
				case "makeAdmin":
					$updateFields = "isAdmin = '1'";
					$actionMsg = "Granting admin privileges to the selected user(s)";
					break;
				case "yankAdmin":
					$updateFields = "isAdmin = '0'";
					$actionMsg = "Revoking admin privileges from the selected user(s)";
					break;
				case "delete":
					$sql = sprintf ("
						DELETE sc_users.*, sc_bookmarks.*, sc_tags.*
						FROM sc_users LEFT JOIN sc_bookmarks
							ON sc_users.uId = sc_bookmarks.uId
						LEFT JOIN sc_tags
							ON sc_bookmarks.bId = sc_tags.bId
						WHERE sc_users.uId IN (%s);
						",
						$userIds
					);
					$actionMsg = "Deleting the selected user(s)";
					break;
				default:
					$tplVars['error'] = "Unrecognized action. No changes were made.";
					return false;
			}

			# since the update statement for almost all of the possible actions except
			# delete are almost totally identical except for which field gets updated,
			# we construct the sql for those actions here, rather than repeating the
			# below code 6 or 7 times over.  this isn't necessarily more efficient,
			# perhaps less so, but it reduces the amount of code
			if ( ! empty($updateFields) ) {
				$sql = sprintf ("
					UPDATE sc_users SET
						%s,
						uModified = '%s'
					WHERE uId IN (%s)
					",
					$updateFields,
       				gmdate('Y-m-d H:i:s', time()),
					$userIds
				);
			}

        	# Execute the sql statement.
			$this->db->sql_transaction('begin');
			if ( ! ($dbresult = & $this->db->sql_query($sql)) ) {
				$this->db->sql_transaction('rollback');
				$tplVars['error'] = "$actionMsg failed.";
				return false;
			}
			$tplVars['msg'] = "$actionMsg was successful.";
			$this->db->sql_transaction('commit');
			return true;
		} else {
			$tplVars['error'] =  "You must select at least one user.";
			return false;
		}

	}

}

?>
