<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "

    CREATE TABLE IF NOT EXISTS `__PREFIX__comments` (
      `commentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `parentID` varchar(64) NOT NULL DEFAULT '',
      `parentTitle` varchar(255) NOT NULL,
      `commentScore` int(10) NOT NULL DEFAULT '0',
      `commentName` varchar(255) NOT NULL DEFAULT '',
      `commentEmail` varchar(255) NOT NULL DEFAULT '',
      `commentURL` varchar(255) NOT NULL DEFAULT '',
      `commentIP` int(10) signed NOT NULL DEFAULT '0',
      `commentDateTime` datetime NOT NULL,
      `commentHTML` text NOT NULL,
      `commentStatus` enum('LIVE','PENDING','SPAM','REJECTED') NOT NULL DEFAULT 'PENDING',
      `commentSpamData` text NOT NULL,
      `commentDynamicFields` text NOT NULL,
      PRIMARY KEY (`commentID`),
      KEY `idx_parentID` (`parentID`),
      KEY `idx_status` (`commentStatus`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

    CREATE TABLE `__PREFIX__comments_votes` (
      `voteID` int(10) NOT NULL AUTO_INCREMENT,
      `commentID` int(10) unsigned NOT NULL,
      `voteValue` int(10) NOT NULL DEFAULT '0',
      `voterID` char(32) NOT NULL DEFAULT '',
      `voteDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`voteID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
    ";

    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }


    $API = new PerchAPI(1.0, 'perch_comments');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_comments', 'Access comments');
    $UserPrivileges->create_privilege('perch_comments.moderate', 'Moderate comments');


    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);

    return $result;