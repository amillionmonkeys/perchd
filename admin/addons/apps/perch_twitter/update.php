<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    $API = new PerchAPI(1.0, 'perch_twitter');

    $Settings = $API->get('Settings');

    if ($Settings->get('perch_twitter_update')->val()!='3.5') {
               
        $db = $API->get('DB');
        
        $sql = 'ALTER TABLE '.PERCH_DB_PREFIX.'twitter_tweets ADD tweetTimeOffset INT(10) NOT NULL DEFAULT \'0\' AFTER tweetDate';
        $db->execute($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".PERCH_DB_PREFIX."twitter_scheduled_tweets` (
                  `tweetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `tweetStatus` char(140) NOT NULL DEFAULT '',
                  `tweetSendDate` datetime DEFAULT '2030-01-01 00:00:00',
                  `tweetSent` tinyint(3) unsigned NOT NULL DEFAULT '0',
                  PRIMARY KEY (`tweetID`),  
                  KEY `idx_sent` (`tweetSent`),
                  KEY `idx_date` (`tweetSendDate`)
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        $db->execute($sql);


        $Settings->set('perch_twitter_update', '3.5');

    }

	

