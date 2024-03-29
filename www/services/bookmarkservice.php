<?php
class BookmarkService {
    var $db;

    function & getInstance(& $db) {
        static $instance;
        if (!isset ($instance))
            $instance = & new BookmarkService($db);
        return $instance;
    }

    function BookmarkService(& $db) {
        $this->db = & $db;
    }

    function _getbookmark($fieldname, $value, $all = false) {
        if (!$all) {
            $userservice = & ServiceFactory :: getServiceInstance('UserService');
            $sId = $userservice->getCurrentUserId();
            $range = ' AND uId = '. $sId;
        }

        $query = 'SELECT * FROM '. $GLOBALS['tableprefix'] .'bookmarks WHERE '. $fieldname .' = "'. $this->db->sql_escape($value) .'"'. $range;

        if (!($dbresult = & $this->db->sql_query_limit($query, 1, 0))) {
            message_die(GENERAL_ERROR, 'Could not get bookmark', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        if ($row =& $this->db->sql_fetchrow($dbresult)) {
            return $row;
        } else {
            return false;
        }
    }

    function & getBookmark($bid, $include_tags = false) {
        if (!is_numeric($bid))
            return;

        $sql = 'SELECT * FROM '. $GLOBALS['tableprefix'] .'bookmarks WHERE bId = '. $this->db->sql_escape($bid);

        if (!($dbresult = & $this->db->sql_query($sql)))
            message_die(GENERAL_ERROR, 'Could not get vars', '', __LINE__, __FILE__, $sql, $this->db);

        if ($row = & $this->db->sql_fetchrow($dbresult)) {
            if ($include_tags) {
                $tagservice = & ServiceFactory :: getServiceInstance('TagService');
                $row['tags'] = $tagservice->getTagsForBookmark($bid);
                $row = $this->addCcFields($row); // Break out CC-specific fields
            }
            return $row;
        } else {
            return false;
        }
    }

    function getBookmarkByAddress($address) {
        $hash = md5($address);
        return $this->getBookmarkByHash($hash);
    }

    function getBookmarkByHash($hash) {
        return $this->_getbookmark('bHash', $hash, true);
    }

    function editAllowed($bookmark) {
        if (!is_numeric($bookmark) && (!is_array($bookmark) || !is_numeric($bookmark['bId'])))
            return false;

        if (!is_array($bookmark))
            if (!($bookmark = $this->getBookmark($bookmark)))
                return false;

        $userservice = & ServiceFactory :: getServiceInstance('UserService');
        $userid = $userservice->getCurrentUserId();
        if ($userservice->isAdminByUID($userid))
            return true;
        else
            return ($bookmark['uId'] == $userid);
    }

    function bookmarkExists($address = false, $uid = NULL) {
        if (!$address) {
            return;
        }

        // If address doesn't contain ":", add "http://" as the default protocol
        if (strpos($address, ':') === false) {
            $address = 'http://'. $address;
        }

        $crit = array ('bHash' => md5($address));
        if (isset ($uid)) {
            $crit['uId'] = $uid;
        }

        $sql = 'SELECT COUNT(*) FROM '. $GLOBALS['tableprefix'] .'bookmarks WHERE '. $this->db->sql_build_array('SELECT', $crit);
        if (!($dbresult = & $this->db->sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Could not get vars', '', __LINE__, __FILE__, $sql, $this->db);
        }
        return ($this->db->sql_fetchfield(0, 0) > 0);
    }

    // Adds a bookmark to the database.
    // Note that date is expected to be a string that's interpretable by strtotime().
    function addBookmark($address, $title, $description, $status, $categories, $date = NULL, $fromApi = false, $fromImport = false) {
        $userservice = & ServiceFactory :: getServiceInstance('UserService');
        $sId = $userservice->getCurrentUserId();

        // If bookmark address doesn't contain ":", add "http://" to the start as a default protocol
        if (strpos($address, ':') === false) {
            $address = 'http://'. $address;
        }

        // Get the client's IP address and the date; note that the date is in GMT.
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else
            if (getenv('REMOTE_ADDR'))
                $ip = getenv('REMOTE_ADDR');
            else
                $ip = getenv('HTTP_X_FORWARDED_FOR');

        // Note that if date is NULL, then it's added with a date and time of now, and if it's present,
        // it's expected to be a string that's interpretable by strtotime().
        if (is_null($date))
            $time = time();
        else
            $time = strtotime($date);
        $datetime = gmdate('Y-m-d H:i:s', $time);

        // Set up the SQL insert statement and execute it.
        $values = array('uId' => intval($sId), 'bIp' => $ip, 'bDatetime' => $datetime, 'bModified' => $datetime, 'bTitle' => $title, 'bAddress' => $address, 'bDescription' => $description, 'bStatus' => intval($status), 'bHash' => md5($address));
        $sql = 'INSERT INTO '. $GLOBALS['tableprefix'] .'bookmarks '. $this->db->sql_build_array('INSERT', $values);
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not insert bookmark', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        // Get the resultant row ID for the bookmark.
        $bId = $this->db->sql_nextid($dbresult);
        if (!isset($bId) || !is_int($bId)) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not insert bookmark', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }

        $uriparts = explode('.', $address);
        $extension = end($uriparts);
        unset($uriparts);

        $tagservice = & ServiceFactory :: getServiceInstance('TagService');
        if (!$tagservice->attachTags($bId, $categories, $fromApi, $extension, false, $fromImport)) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not insert bookmark', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');
        // Everything worked out, so return the new bookmark's bId.
        return $bId;
    }

    function updateBookmark($bId, $address, $title, $description, $status, $categories, $date = NULL, $fromApi = false) {
        if (!is_numeric($bId))
            return false;

        // Get the client's IP address and the date; note that the date is in GMT.
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else
            if (getenv('REMOTE_ADDR'))
                $ip = getenv('REMOTE_ADDR');
            else
                $ip = getenv('HTTP_X_FORWARDED_FOR');

        $moddatetime = gmdate('Y-m-d H:i:s', time());

        // Set up the SQL update statement and execute it.
        $updates = array('bModified' => $moddatetime, 'bTitle' => $title, 'bAddress' => $address, 'bDescription' => $description, 'bStatus' => $status, 'bHash' => md5($address));

        if (!is_null($date)) {
            $datetime = gmdate('Y-m-d H:i:s', strtotime($date));
            $updates[] = array('bDateTime' => $datetime);
        }

        $sql = 'UPDATE '. $GLOBALS['tableprefix'] .'bookmarks SET '. $this->db->sql_build_array('UPDATE', $updates) .' WHERE bId = '. intval($bId);
        $this->db->sql_transaction('begin');

        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update bookmark', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }

        $uriparts = explode('.', $address);
        $extension = end($uriparts);
        unset($uriparts);

        $tagservice = & ServiceFactory :: getServiceInstance('TagService');
        if (!$tagservice->attachTags($bId, $categories, $fromApi, $extension)) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update bookmark', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }

        $this->db->sql_transaction('commit');
        // Everything worked out, so return true.
        return true;
    }

    function & getBookmarks($start = 0, $perpage = NULL, $user = NULL, $tags = NULL, $terms = NULL, $sortOrder = NULL, $watched = NULL, $startdate = NULL, $enddate = NULL, $hash = NULL) {
        // Only get the bookmarks that are visible to the current user.  Our rules:
        //  - if the $user is NULL, that means get bookmarks from ALL users, so we need to make
        //    sure to check the logged-in user's watchlist and get the contacts-only bookmarks from
        //    those users. If the user isn't logged-in, just get the public bookmarks.
        //  - if the $user is set and isn't the logged-in user, then get that user's bookmarks, and
        //    if that user is on the logged-in user's watchlist, get the public AND contacts-only
        //    bookmarks; otherwise, just get the public bookmarks.
        //  - if the $user is set and IS the logged-in user, then get all bookmarks.
        $userservice =& ServiceFactory::getServiceInstance('UserService');
        $tagservice =& ServiceFactory::getServiceInstance('TagService');
        $sId = $userservice->getCurrentUserId();

        if ($userservice->isLoggedOn()) {
            // All public bookmarks, user's own bookmarks and any shared with user
            $privacy = ' AND ((B.bStatus = 0) OR (B.uId = '. $sId .')';
            $watchnames = $userservice->getWatchNames($sId, true);
            foreach($watchnames as $watchuser) {
                $privacy .= ' OR (U.username = "'. $watchuser .'" AND B.bStatus = 1)'; 
            }
            $privacy .= ')';
        } else {
            // Just public bookmarks
            $privacy = ' AND B.bStatus = 0';
        }

        // Set up the tags, if need be.
        if (!is_array($tags) && !is_null($tags)) {
            $tags = explode('+', trim($tags));
        }

        $tagcount = count($tags);
        for ($i = 0; $i < $tagcount; $i ++) {
            $tags[$i] = trim($tags[$i]);
        }

        // Set up the SQL query.
        $query_1 = 'SELECT DISTINCT ';
        if (SQL_LAYER == 'mysql4') {
            $query_1 .= 'SQL_CALC_FOUND_ROWS ';
        }
        $query_1 .= 'B.*, U.'. $userservice->getFieldName('username');

        $query_2 = ' FROM '. $userservice->getTableName() .' AS U, '. $GLOBALS['tableprefix'] .'bookmarks AS B';

        $query_3 = ' WHERE B.uId = U.'. $userservice->getFieldName('primary') . $privacy;
        if (is_null($watched)) {
            if (!is_null($user)) {
                $query_3 .= ' AND B.uId = '. $user;
            }
        } else {
            $arrWatch = $userservice->getWatchlist($user);
            if (count($arrWatch) > 0) {
                foreach($arrWatch as $row) {
                    $query_3_1 .= 'B.uId = '. intval($row) .' OR ';
                }
                $query_3_1 = substr($query_3_1, 0, -3);
            } else {
                $query_3_1 = 'B.uId = -1';
            }
            $query_3 .= ' AND ('. $query_3_1 .') AND B.bStatus IN (0, 1)';
        }

        switch($sortOrder) {
            case 'date_asc':
                $query_5 = ' ORDER BY B.bDatetime ASC ';
                break;
            case 'title_desc':
                $query_5 = ' ORDER BY B.bTitle DESC ';
                break;
            case 'title_asc':
                $query_5 = ' ORDER BY B.bTitle ASC ';
                break;
            case 'url_desc':
                $query_5 = ' ORDER BY B.bAddress DESC ';
                break;
            case 'url_asc':
                $query_5 = ' ORDER BY B.bAddress ASC ';
                break;
            default:
                $query_5 = ' ORDER BY B.bDatetime DESC ';
        }

        // Handle the parts of the query that depend on any tags that are present.
        $query_4 = '';
        for ($i = 0; $i < $tagcount; $i ++) {
            $query_2 .= ', '. $GLOBALS['tableprefix'] .'tags AS T'. $i;
            $query_4 .= ' AND T'. $i .'.tag = "'. $this->db->sql_escape($tags[$i]) .'" AND T'. $i .'.bId = B.bId';
        }

        // Search terms
        if ($terms) {
            // Multiple search terms okay
            $aTerms = explode(' ', $terms);
            $aTerms = array_map('trim', $aTerms);

            // Search terms in tags as well when none given
            if (!count($tags)) {
                $query_2 .= ' LEFT JOIN '. $GLOBALS['tableprefix'] .'tags AS T ON B.bId = T.bId';
                $dotags = true;
            } else {
                $dotags = false;
            }

            $query_4 = '';
            for ($i = 0; $i < count($aTerms); $i++) {
                $query_4 .= ' AND (B.bTitle LIKE "%'. $this->db->sql_escape($aTerms[$i]) .'%"';
                $query_4 .= ' OR B.bDescription LIKE "%'. $this->db->sql_escape($aTerms[$i]) .'%"';
                if ($dotags) {
                    $query_4 .= ' OR T.tag = "'. $this->db->sql_escape($aTerms[$i]) .'"';
                }
                $query_4 .= ')';
            }
        }

        // Start and end dates
        if ($startdate) {
            $query_4 .= ' AND B.bDatetime > "'. $startdate .'"';
        }
        if ($enddate) {
            $query_4 .= ' AND B.bDatetime < "'. $enddate .'"';
        }

        // Hash
        if ($hash) {
            $query_4 .= ' AND B.bHash = "'. $hash .'"';
        }

        $queryFlag = " AND U.isFlagged = 0 ";
        $query = $query_1 . $query_2 . $query_3 . $query_4 . $queryFlag . $query_5;
        if (!($dbresult = & $this->db->sql_query_limit($query, intval($perpage), intval($start)))) {
            message_die(GENERAL_ERROR, 'Could not get bookmarks', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        if (SQL_LAYER == 'mysql4') {
            $totalquery = 'SELECT FOUND_ROWS() AS total';
        } else {
            $totalquery = 'SELECT COUNT(*) AS total '. $query_2 . $query_3 . $query_4 . $queryFlag;
        }

        if (!($totalresult = & $this->db->sql_query($totalquery)) || (!($row = & $this->db->sql_fetchrow($totalresult)))) {
            message_die(GENERAL_ERROR, 'Could not get total bookmarks', '', __LINE__, __FILE__, $totalquery, $this->db);
            return false;
        }


        $total = $row['total'];

        $bookmarks = array();
        while ($row = & $this->db->sql_fetchrow($dbresult)) {
            $row = $this->addCcFields($row); // Break out CC-specific fields
            $row['tags'] = $tagservice->getTagsForBookmark(intval($row['bId']));
            $bookmarks[] = $row;
        }

        return array ('bookmarks' => $bookmarks, 'total' => $total);
    }

    function deleteBookmark($bookmarkid) {
        $query = 'DELETE FROM '. $GLOBALS['tableprefix'] .'bookmarks WHERE bId = '. intval($bookmarkid);
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($query))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not delete bookmarks', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $query = 'DELETE FROM '. $GLOBALS['tableprefix'] .'tags WHERE bId = '. intval($bookmarkid);
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($query))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not delete bookmarks', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $this->db->sql_transaction('commit');
        return true;
    }

    function countOthers($address) {
        if (!$address) {
            return false;
        }

        $userservice = & ServiceFactory :: getServiceInstance('UserService');
        $sId = $userservice->getCurrentUserId();

        if ($userservice->isLoggedOn()) {
            // All public bookmarks, user's own bookmarks and any shared with user
            $privacy = ' AND ((B.bStatus = 0) OR (B.uId = '. $sId .')';
            $watchnames = $userservice->getWatchNames($sId, true);
            foreach($watchnames as $watchuser) {
                $privacy .= ' OR (U.username = "'. $watchuser .'" AND B.bStatus = 1)'; 
            }
            $privacy .= ')';
        } else {
            // Just public bookmarks
            $privacy = ' AND B.bStatus = 0';
        }

        $sql = 'SELECT COUNT(*) FROM '. $userservice->getTableName() .' AS U, '. $GLOBALS['tableprefix'] .'bookmarks AS B WHERE U.'. $userservice->getFieldName('primary') .' = B.uId AND B.bHash = "'. md5($address) .'"'. $privacy;
        if (!($dbresult = & $this->db->sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Could not get vars', '', __LINE__, __FILE__, $sql, $this->db);
        }
        return $this->db->sql_fetchfield(0, 0) - 1;
    }



	/*
	 * Functions added by Nathan Kinkade
	 */

	function flagBookmark($bId) {
		
		if ( ! empty($bId) && is_numeric($bId) ) {
	
			# get the current users ID.  if for some reason they got here and are
			# not actually logged in, then just reject the request
            $userservice = & ServiceFactory :: getServiceInstance('UserService');
			if ( ! $currentUserId = $userservice->getCurrentUserid() ) {
				return "notLoggedIn";
			}

			# first we make sure that this user hasn't already flagged this bookmark
			$sql = sprintf ("
				SELECT bFlaggedBy FROM %sbookmarks
				WHERE bId = '%s'
				",
				$GLOBALS['tableprefix'],
				$bId
			);
			$qid = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($qid);
			$flaggedBy = explode(":", $row['bFlaggedBy']);

			if ( $flaggedBy && in_array($currentUserId, $flaggedBy) ) {
				# if they have already flagged it, then stop here
				return "alreadyFlagged";
			} else {
				# update the flag count by one and add this user to the list of users
				# who have flagged this bookmark
				$sql = sprintf ("
					UPDATE %sbookmarks SET
						bFlagCount = (bFlagCount + 1),
						bFlaggedBy = CONCAT(IFNULL(bFlaggedBy,''),':','%s')
					WHERE bId = '%s'
					",
					$GLOBALS['tableprefix'],
					$currentUserId,
					$bId
				);
				$qid = $this->db->sql_query($sql);
				# 1 record should have been updated, if not, then the most likely
				# cause was a bad bookmark Id, but it could have been a db error
				if ( 1 == $this->db->sql_affectedrows($qid) ) {
					$sql = sprintf ("
						SELECT bFlagCount FROM %sbookmarks
						WHERE bId = '%s'
						",
						$GLOBALS['tableprefix'],
						$bId
					);
					$qid = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($qid);
					return $row['bFlagCount'];
				} else {
					return "noUpdate";
				}
			}
		} else {
			# if the bookmark id was null or not a number, then something went
			# very wrong
			return "invalid";
		}

	}

	function getFlaggedBookmarks() {

		$sql = sprintf ("
			SELECT %sbookmarks.*, %susers.username
		  	FROM %sbookmarks INNER JOIN %susers
				ON %sbookmarks.uId = %susers.uId
			WHERE bFlagCount > 0
			ORDER BY bFlagCount DESC, bTitle
			",
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix']
		);
		$qid = $this->db->sql_query($sql);
		$bookmarks = $this->db->sql_fetchrowset($qid);

		# let's grab the username(s) of the people who flagged the various bookmarks.
		# this is a convenience for the admin
		for ( $idx = 0; $idx < count($bookmarks); $idx++ ) {
			# strip off any extraneous leading or trailing colons, and there will always
			# be an extra one at the beginning because the method that inserts appends
			# users doesn't bother to check to see if it's null/empty and always inserts
			# ":<uId>".  this is on purpose because it seems easier to do this than to
			# bother checking if the field was empty using SQL.
			$userIds = trim($bookmarks[$idx]['bFlaggedBy'], ":");
			# replace the colon with a comman which is usable in a sql statement
			$userIds = preg_replace("/:/", ",", $userIds);
			# grab the users who flagged this bookmark
			if ( ! empty($userIds) ) {
				$sql = sprintf ("
					SELECT username FROM %susers
					WHERE uId IN ($userIds)
					",
					$GLOBALS['tableprefix']
				);
				$qid = $this->db->sql_query($sql);
				$flaggers = array();
				while ( $row = $this->db->sql_fetchrow($qid) ) {
					$flaggers[] = $row['username'];
				}
				# turn the array into a comma separated string and then assign
				# it to our bookmarks array
				$flaggedBy = implode(",", $flaggers);
				$bookmarks[$idx]['flaggedBy'] = $flaggedBy;
			}
		}

		return $bookmarks;

	}

	# make changes to bookmarks
	function modifyBookmarks() {

		global $tplVars;

		# initialize a sql string
		$sql = "";

		if ( isset($_POST['bookmarkList']) ) {
			# pull list of bookmark ids from the submitted form
			$bookmarkIds = implode(",", $_POST['bookmarkList']);
			switch ( $_POST['bAction'] ) {
				case "unflag":
					$sql = sprintf ("
						UPDATE %sbookmarks SET
							bFlagCount = '0',
							bModified = '%s',
							bFlaggedBy = ''
						WHERE bId IN (%s)
						",
						$GLOBALS['tableprefix'], 
        				gmdate('Y-m-d H:i:s', time()),
						$bookmarkIds
					);
					$actionMsg = "Unflagging the selected bookmark(s)";
					break;
				case "disable":
					$sql = sprintf ("
						UPDATE %sbookmarks SET
							bStatus = '1',
							bModified = '%s'
						WHERE bId IN (%s)
						",
						$GLOBALS['tableprefix'], 
						gmdate('Y-m-d H:i:s', time()),
						$bookmarkIds
					);
					$actionMsg = "Disabling the selected bookmark(s)";
					break;
				case "enable":
					$sql = sprintf ("
						UPDATE %sbookmarks SET
							bStatus = '0',
							bModified = '%s'
						WHERE bId IN (%s)
						",
						$GLOBALS['tableprefix'], 
						gmdate('Y-m-d H:i:s', time()),
						$bookmarkIds
					);
					$actionMsg = "Enabling the selected bookmark(s)";
					break;
				case "delete":
					$sql = sprintf ("
						DELETE %sbookmarks.*, %stags.*
						FROM %sbookmarks LEFT JOIN %stags
							ON %sbookmarks.bId = %stags.bId
						WHERE %sbookmarks.bId IN (%s);
						",
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$GLOBALS['tableprefix'],
						$bookmarkIds
					);
					$actionMsg = "Deleting the selected bookmark(s)";
					break;
				default:
					$tplVars['error'] = "Unrecognized action. No changes were made.";
					return false;
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
			$tplVars['error'] =  "You must select at least one bookmark.";
			return false;
		}

	}


	function getAllBookmarks() {
		$sql = sprintf ("
			SELECT %sbookmarks.bId, %sbookmarks.bAddress, GROUP_CONCAT(%stags.tag) AS bTags
			FROM %sbookmarks LEFT JOIN %stags
				ON %sbookmarks.bId = %stags.bId
			WHERE %sbookmarks.bFlagCount < '1' 
			GROUP BY %sbookmarks.bId
			",
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix'],
			$GLOBALS['tableprefix']
		);
		$qid = $this->db->sql_query($sql);
		return $this->db->sql_fetchrowset($qid);
	}


    /**
     * We are storing certain CC-specific data as tags in the form of 
     * cc:<fieldname>::<value>.  This is more extensible and flexible than 
     * altering the database to add/remove fields.  This function will take a 
     * given bookmark record and then fetch any CC specific tags and will add 
     * them to the record so that they appear to other parts of the system as 
     * regular fields.
     */
    function addCcFields($record) {

        #print_r($record);exit;
        $tagservice = & ServiceFactory :: getServiceInstance('TagService');
        $ccTags = $tagservice->getCcTagsForBookmark(intval($record['bId']));
        if ( count($ccTags) ) {
            foreach ( $ccTags as $tag ) {
                $fieldName = substr($tag, 3); // Strip the 'cc:' part
                // Break the special tag into token and value
                list($token,$value) = explode('::', $fieldName);
                // Make the field look like other Scuttle fields by
                // prepending a "b".  Aesthetics.
                $fieldName = "b" . ucfirst($token);
                $record[$fieldName] = $value;
            }
        }

        return $record;

    }


}

?>
