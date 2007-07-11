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
        $seed = (integer) md5(microtime());
        mt_srand($seed);
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

    function getUsersByCategory() {
        $query = 'SELECT * FROM sc_users ORDER BY isAdmin DESC, isFlagged, username';
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
            return false;
            }
        $ordered_users = array(
            'admins' => array(),
            'registered' => array(),
            'unregistered' => array(),
            'flagged' => array()
        );
        while ($row = $this->db->sql_fetchrow($dbresult)) {
            if($row['isAdmin']) { // administrator
                array_push($ordered_users['admins'], $row);
            }
            else if (!$row['uStatus']) { // unregistred user
                array_push($ordered_users['unregistered'], $row);
            }
            else if (!$row['isFlagged']) { //registered user
                array_push($ordered_users['registered'], $row);
            }
            else { // flagged user
                array_push($ordered_users['flagged'], $row);
            }
        }
        return $ordered_users;
    }

    function deleteUserByUsername($username)
    {
        $username = $this->safeString($username);
        $u = $this->_getuser($this->getFieldName('username'), $username);;
        $uId = $u['uId'];
        // don't really understand watchlist thing...
        // $d_query0 = "DELETE FROM sc_watched WHERE uId = ".$uId;

        // first, delete the user's tags
        // 1. get the user's tags
        $query = "SELECT DISTINCT u.uId, b.bId, t.id, t.tag 
            FROM sc_users AS u, sc_bookmarks AS b, sc_tags AS t 
            WHERE u.uId = b.uId AND b.bId = t.bId AND u.uId = " . $uId;
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not perform action', '', 
                __LINE__, __FILE__, $query, $this->db);
            return false;
            }
        // 2. put them into a string (for the sql)
        $idString = "(";
        while($row = mysql_fetch_array($dbresult)) { 
             $idString .= $row['id'] . ",";
            }
        $idString[strlen($idString)-1] = ')';

        // 3. then delete the tags
        $query = "DELETE FROM sc_tags WHERE id IN " . $idString;
        if (! ($dbresult =& $this->db->sql_query($query)) ) { 
            message_die(GENERAL_ERROR, 'Could not perform action', '', 
                __LINE__, __FILE__, $query, $this->db);
            return false;
            }

        // then delete the user's bookmarks
        $query = "DELETE FROM sc_bookmarks WHERE uId = ".$uId;
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not perform action', '', 
                __LINE__, __FILE__, $query, $this->db);
            return false;
            }

        // finally, delete the user
        $query = "DELETE FROM sc_users WHERE uId = '". $uId . "'";
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not perform action', '', 
                __LINE__, __FILE__, $query, $this->db);
            return false;
            }
        return true;
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

    function performAdminActions($usersTOactions) {
        $changeFlag = false;
        foreach(array_keys($usersTOactions) as $i) {
            if(strlen($i) > 25) 
                return array(0, "Unrecognized username");
            $executeAtBottom = true;
            switch ($usersTOactions[$i]) {
                case "revoke_admin_access":
                    $i = $this->safeString($i);
                    $query = sprintf("UPDATE sc_users SET isAdmin = 0 WHERE username = '%s'", $i);
                    break;
                case "promote_to_admin":
                    $i = $this->safeString($i);
                    $query = sprintf("UPDATE sc_users SET isAdmin = 1 WHERE username = '%s'", $i);
                    break;
                case "flag":
                    $i = $this->safeString($i);
                    $query = sprintf("UPDATE sc_users SET isFlagged = 1 WHERE username = '%s'", $i);
                    break;
                case "promote_to_regular":
                    $i = $this->safeString($i);
                    $query = sprintf("UPDATE sc_users SET isFlagged = 0 WHERE username = '%s'", $i);
                    break;
                case "approve_registration":
                    $i = $this->safeString($i);
                    $query = sprintf("UPDATE sc_users SET uStatus = 1 WHERE username = '%s'", $i);
                    break;
                case "delete_from_db":
                    $delResult = $this->deleteUserByUsername($i);
                    if(!$delResult[0]) {
                        return $delResult;
                    }
                    $changeFlag = true;
                    $executeAtBottom = false;
                    break;
                default: // handle tags and errors
                    switch($i){
                        case "merge_tag_from":
                            if($usersTOactions['merge_tag_from']) {
                                if ($usersTOactions['merge_tag_to']) { //both filled out
                                    /* Begin SQL Injection prevention */
                                    $merge_tag_to = $this->safeString($usersTOactions['merge_tag_to']);
                                    $merge_tag_from = $this->safeString($usersTOactions['merge_tag_from']);
                                    if(strlen($merge_tag_to) > 30 or strlen($merge_tag_from) > 30)
                                        return array(0, "Please shorten your tag lengths");
                                    $query = sprintf("UPDATE sc_tags SET tag = '%s' WHERE tag = '%s'",
                                            $merge_tag_to, $merge_tag_from);
                                    if ($usersTOactions['merge_trigger_enable']) {
                                        $tagservice =& ServiceFactory::getServiceInstance('TagService');
                                        if(!$tagservice->addEntryToTagMap($usersTOactions['merge_tag_from'],
                                            $usersTOactions['merge_tag_to']))
                                            return array(0, "Tagmap addition failed! (perhaps it can't find the tagmap.array file?)"); 
                                    }
                                }
                                else {
                                    return array(0, "Please fill out both textboxes, not just one");
                                }
                            } elseif ($usersTOactions['merge_tag_to']) { 
                                return array(0, "Please fill out both textboxes, not just one");
                            } else {
                                $executeAtBottom = false;
                            }
                            break;
                        case "merge_tag_to":
                            $executeAtBottom = false;
                            break;
                        case "merge_trigger_enable":
                            if(!($usersTOactions['merge_tag_from'] and $usersTOactions['merge_tag_to']))
                                return array(0, "Please specify the tag conversion you wish to automate");
                            $executeAtBottom = false;
                            break;
                        default:
                            return array(0, "Error in switch");
                    } // inner switch
                } // outter switch

            if ($executeAtBottom) {
                if(!($dbresult =& $this->db->sql_query($query)) ) {
                    message_die(GENERAL_ERROR, 'Could not perform action', '', 
                        __LINE__, __FILE__, $query, $this->db);
                    return array(0, "FAILED QUERY: " . $query);
                }
                else $changeFlag = true;
            }

        } // foreach
        if($changeFlag) return array(1, );
        else return array(2, );
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

    function addUser($username, $password, $email) {
        // Set up the SQL UPDATE statement.
        $datetime = gmdate('Y-m-d H:i:s', time());
        $password = $this->sanitisePassword($password);
        
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
}
?>
