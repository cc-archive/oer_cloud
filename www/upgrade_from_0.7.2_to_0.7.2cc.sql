ALTER TABLE `sc_bookmarks`
ADD KEY `sc_bookmarks_du` (`bDatetime`,`uId`),
ADD KEY `sc_bookmarks_hu` (`bHash`,`uId`);

ALTER TABLE `sc_tags`
ADD KEY `sc_tags_b` (`bId`),
ADD KEY `sc_tags_tb` (`tag`(5),`bId`);

ALTER TABLE `sc_users`
ADD COLUMN  `uIp` varchar(15) default NULL,
ADD COLUMN  `uStatus` tinyint(1) NOT NULL default '0',
ADD COLUMN  `isFlagged` tinyint(1) NOT NULL default '0',
ADD COLUMN  `isAdmin` tinyint(1) NOT NULL default '0',
ADD KEY `sc_users_ui` (`username`(10),`uId`),
ADD KEY `sc_users_pi` (`uIp`(12),`uId`);

CREATE VIEW flagged_tags AS SELECT DISTINCT id, tag FROM sc_users AS u, sc_bookmarks 
AS b, sc_tags AS t WHERE u.uId = b.uId AND b.bId = t.bId AND isFlagged = 1;


