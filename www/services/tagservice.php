<?php
class TagService {
    var $db;
    var $tablename;

    function &getInstance(&$db) {
        static $instance;
        if (!isset($instance))
            $instance =& new TagService($db);
        return $instance;
    }

    function TagService(&$db) {
        $this->db =& $db;
        $this->tablename = $GLOBALS['tableprefix'] .'tags';
    }

    function isNotSystemTag($var) {
        if (utf8_substr($var, 0, 7) == 'system:')
            return false;
        else
            return true;
    }

    function attachTags($bookmarkid, $tags, $fromApi = false, $extension = NULL, $replace = true, $fromImport = false) {
        // Make sure that categories is an array of trimmed strings, and that if the categories are
        // coming in from an API call to add a bookmark, that underscores are converted into strings.
        if (!is_array($tags)) {
            $tags = trim($tags);
            if ($tags != '') {
                if (substr($tags, -1) == ',') {
                    $tags = substr($tags, 0, -1);
                }
                if ($fromApi) {
                    $tags = explode(' ', $tags);
                } else {
                    $tags = explode(',', $tags);
                }
            } else {
                $tags = null;
            }
        }
        
		# Make sure there are tags before we try to look through them
		if ( count($tags) ) {
			# Apply tag mappings
        	$tags = $this->applyTagmaps($tags);

			# do a little input validation on the tags.  change any quotes to their
			# html entity values, and also strip off any double quotes that people
			# try to put around their tags
			foreach ( $tags as $key => $tagItem ) {
				$tags[$key] = htmlspecialchars($tags[$key], ENT_QUOTES);
			}
		}

        $tags_count = count($tags);
        for ($i = 0; $i < $tags_count; $i++) {
            $tags[$i] = trim(utf8_strtolower($tags[$i]));
            if ($fromApi) {
                include_once(dirname(__FILE__) .'/../functions.inc.php');
                $tags[$i] = convertTag($tags[$i], 'in');
            }
        }

        if ($tags_count > 0) {
            // Remove system tags
            $tags = array_filter($tags, array($this, "isNotSystemTag"));

            // Eliminate any duplicate categories
            $temp = array_unique($tags);
            $tags = array_values($temp);
        } else {
            // Unfiled
            $tags[] = 'system:unfiled';
        }

        // Media and file types
        if (!is_null($extension)) {
            include_once(dirname(__FILE__) .'/../functions.inc.php');
            if ($keys = multi_array_search($extension, $GLOBALS['filetypes'])) {
                $tags[] = 'system:filetype:'. $extension;
                $tags[] = 'system:media:'. array_shift($keys);
            }
        }

        // Imported
        if ($fromImport) {
            $tags[] = 'system:imported';
        }

        $this->db->sql_transaction('begin');

        if ($replace) {
            if (!$this->deleteTagsForBookmark($bookmarkid)){
                $this->db->sql_transaction('rollback');
                message_die(GENERAL_ERROR, 'Could not attach tags (deleting old ones failed)', '', __LINE__, __FILE__, $sql, $this->db);
                return false;
            }
        }

        // Add the categories to the DB.
        for ($i = 0; $i < count($tags); $i++) {
            if ($tags[$i] != '') {
                $values = array(
                    'bId' => intval($bookmarkid),
                    'tag' => $tags[$i]
                );

                if (!$this->hasTag($bookmarkid, $tags[$i])) {
                    $sql = 'INSERT INTO '. $this->getTableName() .' '. $this->db->sql_build_array('INSERT', $values);
                    if (!($dbresult =& $this->db->sql_query($sql))) {
                        $this->db->sql_transaction('rollback');
                        message_die(GENERAL_ERROR, 'Could not attach tags', '', __LINE__, __FILE__, $sql, $this->db);
                        return false;
                    }
                }
            }
        }
        $this->db->sql_transaction('commit');
        return true;    
    } 
    
    function deleteTag($tag) {
        $userservice =& ServiceFactory::getServiceInstance('UserService');
        $logged_on_user = $userservice->getCurrentUserId();

        $query = 'DELETE FROM '. $this->getTableName() .' USING '. $GLOBALS['tableprefix'] .'tags, '. $GLOBALS['tableprefix'] .'bookmarks WHERE '. $GLOBALS['tableprefix'] .'tags.bId = '. $GLOBALS['tableprefix'] .'bookmarks.bId AND '. $GLOBALS['tableprefix'] .'bookmarks.uId = '. $logged_on_user .' AND '. $GLOBALS['tableprefix'] .'tags.tag = "'. $this->db->sql_escape($tag) .'"';

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not delete tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        return true;
    }
    
    function deleteTagsForBookmark($bookmarkid) {
        if (!is_int($bookmarkid)) {
            message_die(GENERAL_ERROR, 'Could not delete tags (invalid bookmarkid)', '', __LINE__, __FILE__, $query);
            return false;
        }

        $query = 'DELETE FROM '. $this->getTableName() .' WHERE bId = '. intval($bookmarkid);

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not delete tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        return true;
    }

    function &getTagsForBookmark($bookmarkid) {
        if (!is_int($bookmarkid)) {
            message_die(GENERAL_ERROR, 'Could not get tags (invalid bookmarkid)', '', __LINE__, __FILE__, $query);
            return false;
        }

        $query = 'SELECT tag FROM '. $this->getTableName() .' WHERE bId = '. intval($bookmarkid) .' AND LEFT(tag, 7) <> "system:" AND LEFT(tag, 3) <> "cc:" ORDER BY tag';

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not get tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $tags = array();
        while ($row =& $this->db->sql_fetchrow($dbresult)) {
            $tags[] = $row['tag'];
        }

        return $tags;
    }

    function &getTags($userid = NULL) {
        $userservice =& ServiceFactory::getServiceInstance('UserService');
        $logged_on_user = $userservice->getCurrentUserId();

        $query = 'SELECT T.tag, COUNT(B.bId) AS bCount FROM '. $GLOBALS['tableprefix'] .'bookmarks AS B INNER JOIN '. $userservice->getTableName() .' AS U ON B.uId = U.'. $userservice->getFieldName('primary') .' INNER JOIN '. $GLOBALS['tableprefix'] .'tags AS T ON B.bId = T.bId';

        $conditions = array();
        if (!is_null($userid)) {
            $conditions['U.'. $userservice->getFieldName('primary')] = intval($userid);
            if ($logged_on_user != $userid)
                $conditions['B.bStatus'] = 0;
        } else {
            $conditions['B.bStatus'] = 0;
        }

        $query .= ' WHERE '. $this->db->sql_build_array('SELECT', $conditions) .' AND LEFT(T.tag, 7) <> "system:" AND LEFT(T.tag, 3) <> "cc:" GROUP BY T.tag ORDER BY bCount DESC, tag';

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not get tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }
        return $this->db->sql_fetchrowset($dbresult);
    }
    
  
    // Returns the tags related to the specified tags; i.e. attached to the same bookmarks
    function &getRelatedTags($tags, $for_user = NULL, $logged_on_user = NULL, $limit = 10) {
        $conditions = array();
        // Only count the tags that are visible to the current user.
        if ($for_user != $logged_on_user || is_null($for_user))
            $conditions['B.bStatus'] = 0;

        if (!is_null($for_user))
            $conditions['B.uId'] = $for_user;

        // Set up the tags, if need be.
        if (is_numeric($tags))
            $tags = NULL;
        if (!is_array($tags) and !is_null($tags))
            $tags = explode(',', trim($tags));

        $tagcount = count($tags);
        for ($i = 0; $i < $tagcount; $i++) {
            $tags[$i] = trim($tags[$i]);
        }

        // Set up the SQL query.
        $query_1 = 'SELECT DISTINCTROW T0.tag, COUNT(B.bId) AS bCount FROM '. $GLOBALS['tableprefix'] .'bookmarks AS B, '. $this->getTableName() .' AS T0';
        $query_2 = '';
        $query_3 = ' WHERE B.bId = T0.bId ';
        if (count($conditions) > 0)
            $query_4 = ' AND '. $this->db->sql_build_array('SELECT', $conditions);
        else
            $query_4 = '';
        // Handle the parts of the query that depend on any tags that are present.
        for ($i = 1; $i <= $tagcount; $i++) {
            $query_2 .= ', '. $this->getTableName() .' AS T'. $i;
            $query_4 .= ' AND T'. $i .'.bId = B.bId AND T'. $i .'.tag = "'. $this->db->sql_escape($tags[$i - 1]) .'" AND T0.tag <> "'. $this->db->sql_escape($tags[$i - 1]) .'"';
        }
        $query_5 = ' AND LEFT(T0.tag, 7) <> "system:" AND LEFT(TO.tag, 3) <> "cc:" GROUP BY T0.tag ORDER BY bCount DESC, T0.tag';
        $query = $query_1 . $query_2 . $query_3 . $query_4 . $query_5;

        if (! ($dbresult =& $this->db->sql_query_limit($query, $limit)) ){
            message_die(GENERAL_ERROR, 'Could not get related tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }
        return $this->db->sql_fetchrowset($dbresult);
    }

    // Returns the most popular tags used for a particular bookmark hash
    function &getRelatedTagsByHash($hash, $limit = 20) {
        $userservice = & ServiceFactory :: getServiceInstance('UserService');
        $sId = $userservice->getCurrentUserId();
        // Logged in
        if ($userservice->isLoggedOn()) {
            $arrWatch = $userservice->getWatchList($sId);
            // From public bookmarks or user's own
            $privacy = ' AND ((B.bStatus = 0) OR (B.uId = '. $sId .')';
            // From shared bookmarks in watchlist
            foreach ($arrWatch as $w) {
                $privacy .= ' OR (B.uId = '. $w .' AND B.bStatus = 1)';
            }
            $privacy .= ') ';
        // Not logged in
        } else {
            $privacy = ' AND B.bStatus = 0 ';
        }

        $query = 'SELECT T.tag, COUNT(T.tag) AS bCount FROM sc_bookmarks AS B LEFT JOIN sc_tags AS T ON B.bId = T.bId WHERE B.bHash = "'. $hash .'" '. $privacy .'AND LEFT(T.tag, 7) <> "system:" AND LEFT(T.tag, 3) <> "cc:" GROUP BY T.tag ORDER BY bCount DESC';

        if (!($dbresult =& $this->db->sql_query_limit($query, $limit))) {
            message_die(GENERAL_ERROR, 'Could not get related tags for this hash', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }
        return $this->db->sql_fetchrowset($dbresult);
    }

    function &getPopularTags($user = NULL, $limit = 30, $logged_on_user = NULL, $days = NULL) {
        // Only count the tags that are visible to the current user.
        if (($user != $logged_on_user) || is_null($user) || ($user === false))
            $privacy = ' AND B.bStatus = 0';
        else
            $privacy = '';

        if (is_null($days) || !is_int($days))
            $span = '';
        else
            $span = ' AND B.bDatetime > "'. date('Y-m-d H:i:s', time() - (86400 * $days)) .'"';

        $query = 'SELECT T.tag, COUNT(T.bId) AS bCount FROM '. $this->getTableName() .' AS T, '. $GLOBALS['tableprefix'] .'bookmarks AS B WHERE ';
        if (is_null($user) || ($user === false)) {
            $query .= 'B.bId = T.bId AND B.bStatus = 0';
        } else {
            $query .= 'B.uId = '. $this->db->sql_escape($user) .' AND B.bId = T.bId'. $privacy;
        }
        $query .= $span .' AND LEFT(T.tag, 7) <> "system:" AND LEFT(T.tag, 3) <> "cc:" GROUP BY T.tag ORDER BY bCount DESC, tag';

        if (!($dbresult =& $this->db->sql_query_limit($query, $limit))) {
            message_die(GENERAL_ERROR, 'Could not get popular tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        return $this->db->sql_fetchrowset($dbresult);
    }

    function hasTag($bookmarkid, $tag) {
        $query = 'SELECT COUNT(*) AS tCount FROM '. $this->getTableName() .' WHERE bId = '. intval($bookmarkid) .' AND tag ="'. $this->db->sql_escape($tag) .'"';

        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not find tag', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }
        
        if ($row =& $this->db->sql_fetchrow($dbresult)) {
            if ($row['tCount'] > 0) {
                return true;
            }
        }
        return false;
    }

    function renameTag($userid, $old, $new, $fromApi = false) {
        $bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');

        if (is_null($userid) || is_null($old) || is_null($new))
            return false;

        // Find bookmarks with old tag
        $bookmarksInfo =& $bookmarkservice->getBookmarks(0, NULL, $userid, $old);
        $bookmarks =& $bookmarksInfo['bookmarks'];

        // Delete old tag
        $this->deleteTag($old);

        // Attach new tags
        foreach(array_keys($bookmarks) as $key) {
            $row =& $bookmarks[$key];
            $this->attachTags($row['bId'], $new, $fromApi, NULL, false);
        }

        return true;
    }

    function &tagCloud($tags = NULL, $steps = 5, $sizemin = 90, $sizemax = 225, $sortOrder = NULL) {

        if (is_null($tags) || count($tags) < 1) {
            return false;
        }

        $min = $tags[count($tags) - 1]['bCount'];
        $max = $tags[0]['bCount'];

        for ($i = 1; $i <= $steps; $i++) {
            $delta = ($max - $min) / (2 * $steps - $i);
            $limit[$i] = $i * $delta + $min;
        }
        $sizestep = ($sizemax - $sizemin) / $steps;
        foreach ($tags as $row) {
            $next = false;
            for ($i = 1; $i <= $steps; $i++) {
                if (!$next && $row['bCount'] <= $limit[$i]) {
                    $size = $sizestep * ($i - 1) + $sizemin;
                    $next = true;
                }
            }
            $tempArray = array('size' => $size .'%');
            $row = array_merge($row, $tempArray); 
            $output[] = $row;
        }

        if ($sortOrder == 'alphabet_asc') {
            usort($output, create_function('$a,$b','return strcasecmp(utf8_deaccent($a["tag"]), utf8_deaccent($b["tag"]));'));
        }

        return $output;
    }

    // Properties
    function getTableName()       { return $this->tablename; }
    function setTableName($value) { $this->tablename = $value; }

	/*
	 * Function added by Nathan Kinkade
	 */

	function getTagmaps() {
		
		$sql = sprintf ("
			SELECT * FROM %stagmaps
			ORDER BY fromTag
			",
			$GLOBALS['tableprefix']
		);
		$qid = $this->db->sql_query($sql);
		$tagmaps = $this->db->sql_fetchrowset($qid);

		return $tagmaps;

	}

	# rename tags
	function renameTags() {

		global $tplVars;

		# strip white space off beginning and end
		if ( isset($_POST['doRenameTags']) ) {

			$fromTag = trim($_POST['fromTag']);
			$toTag = trim($_POST['toTag']);

			if ( (strlen($fromTag) > 32) || (strlen($toTag) > 32) ) {
				$tplVars['error'] = "Tag names cannot be longer than 32 characters.";
				return false;
			}

			# don't let the script continue if either the source or target tag name is missing
			if ( empty($fromTag) || empty($toTag) ) {
				$tplVars['error'] = "You must specify both a source tag name and a target tag name.";
				return false;
			}

			$sql = sprintf ("
				UPDATE %stags SET
					tag = '%s'
				WHERE tag = '%s'
				",
				$GLOBALS['tableprefix'],
				$this->db->sql_escape($toTag),
				$this->db->sql_escape($fromTag)
			);

			if ( $this->db->sql_query($sql) ) {
				$tplVars['msg'] = "All tags with name '$fromTag' were successfully renamed to '$toTag'.";
			} else {
				$tplVars['error'] = "Renaming tags from '$fromTag' to '$toTag' failed.";
			}

			if ( isset($_POST['persistentTagmap']) ) {
				if ( $this->addTagmap($fromTag, $toTag) ) {
					$tplVars['msg'] .= " The tagmap was made persistent.";
				} else {
					$tplVars['error'] .= " Making the tagmap persistent failed.";
				}
			}

			return true;

		} else {
			$tplVars['error'] = "This script was not accessed correctly.  Exiting ...";
			return false;
		}

	}

	function addTagmap($fromTag, $toTag) {

		$sql = sprintf ("
			INSERT INTO %stagmaps (fromTag, toTag)
			VALUES ('%s','%s')
			",
			$GLOBALS['tableprefix'],
			$this->db->sql_escape($fromTag),
			$this->db->sql_escape($toTag)
		);
		if ( $this->db->sql_query($sql) ) {
			return true;
		} else {
			return false;
		}

	}

	function modifyTagmaps() {

		global $tplVars;

		if ( isset($_POST['tagmapList']) ) {
			# pull list of tagmap ids from the submitted form
			$tagmapIds = implode(",", $_POST['tagmapList']);
			switch ( $_POST['tAction'] ) {
				case "delete":
					$sql = sprintf ("
						DELETE FROM %stagmaps
						WHERE id IN (%s)
						",
						$GLOBALS['tableprefix'],
						$tagmapIds
					);
					$actionMsg = "Deleting the selected tag mapping(s)";
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
			$tplVars['error'] = "You must select at least one tag mapping.";
			return false;
		}

	}

	function applyTagmaps($tags) {

		if ( $tags ) {
			for ( $idx = 0; $idx < count($tags); $idx++ ) {
				$fromTag = trim($tags[$idx]);
				$sql = sprintf ("
					SELECT * from %stagmaps
					WHERE fromTag = '%s'
					",
					$GLOBALS['tableprefix'],
					$fromTag
				);
				$qid = $this->db->sql_query($sql);
				$mapping = $this->db->sql_fetchrow($qid);
				if ( $mapping ) {
					$tags[$idx] = $mapping['toTag'];
				}
			}
		}

		return $tags;

	}


	# return a list of every distinct tag in the database
	function getAllTags() {

		$sql = sprintf ("
			SELECT DISTINCT tag FROM %stags
			ORDER BY tag
			",
			$GLOBALS['tableprefix']
		);
		$qid = $this->db->sql_query($sql);
		return $this->db->sql_fetchrowset($qid);

	}

    /**
     * This function will return any special CC tags that store things about 
     * a bookmark like it's license type, subject, grade level, language, etc.
     */
    function getCcTagsForBookmark($bookmarkid) {
        if (!is_int($bookmarkid)) {
            message_die(GENERAL_ERROR, 'Could not get CC tags (invalid bookmarkid)', '', __LINE__, __FILE__, $query);
            return false;
        }

        $query = 'SELECT tag FROM '. $this->getTableName() .' WHERE bId = '. intval($bookmarkid) .' AND LEFT(tag, 3) = "cc:" ORDER BY tag';

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not get CC tags', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $tags = array();
        while ($row =& $this->db->sql_fetchrow($dbresult)) {
            $tags[] = $row['tag'];
        }

        return $tags;
    }

}

?>
