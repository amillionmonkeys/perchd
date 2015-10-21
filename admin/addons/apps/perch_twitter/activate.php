<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
    CREATE TABLE IF NOT EXISTS `__PREFIX__twitter_tweets` (
      `tweetID` int(11) NOT NULL AUTO_INCREMENT,
      `tweetTwitterID` varchar(255) NOT NULL DEFAULT '',
      `tweetUser` varchar(255) NOT NULL DEFAULT '',
      `tweetUserRealName` varchar(255) NOT NULL DEFAULT '',
      `tweetUserAvatar` varchar(255) NOT NULL DEFAULT '',
      `tweetDate` datetime DEFAULT NULL,
      `tweetTimeOffset` int(10) NOT NULL DEFAULT '0',
      `tweetText` varchar(255) NOT NULL DEFAULT '',
      `tweetHTML` text,
      `tweetType` enum('mine','favorites') NOT NULL DEFAULT 'mine',
      `tweetAccount` varchar(255) NOT NULL DEFAULT '',
      `tweetIsReply` int(10) NOT NULL DEFAULT 0,
      PRIMARY KEY (`tweetID`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
    CREATE TABLE IF NOT EXISTS `__PREFIX__twitter_settings` (
      `settingID` int(11) NOT NULL AUTO_INCREMENT,
      `settingTwitterID` varchar(255) NOT NULL DEFAULT '',
      `settingTwitterKey` varchar(255) NOT NULL DEFAULT '',
      `settingTwitterSecret` varchar(255) NOT NULL DEFAULT '',
      `settingTwitterToken` varchar(255) NOT NULL DEFAULT '',
      `settingTwitterTokenSecret` varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`settingID`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

    CREATE TABLE IF NOT EXISTS `__PREFIX__twitter_scheduled_tweets` (
      `tweetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `tweetStatus` char(140) NOT NULL DEFAULT '',
      `tweetSendDate` datetime DEFAULT '2030-01-01 00:00:00',
      `tweetSent` tinyint(3) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`tweetID`),  
      KEY `idx_sent` (`tweetSent`),
      KEY `idx_date` (`tweetSendDate`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        
    INSERT INTO `__PREFIX__twitter_settings` (settingTwitterID) VALUES ('')";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }
        
    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;
