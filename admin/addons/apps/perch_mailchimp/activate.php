<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
      CREATE TABLE IF NOT EXISTS `__PREFIX__mailchimp_stats` (
        `statID` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL DEFAULT '',
        `total` varchar(255) NOT NULL DEFAULT '',
        `yesterday` varchar(255) NOT NULL DEFAULT '',
        `today` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`statID`)
      ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
          
      CREATE TABLE IF NOT EXISTS `__PREFIX__mailchimp_history` (
        `historyID` int(11) NOT NULL AUTO_INCREMENT,
        `historyTotal` varchar(255) NOT NULL DEFAULT '',
        `historyDate` date DEFAULT NULL,
        PRIMARY KEY (`historyID`)
      ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
      
      CREATE TABLE IF NOT EXISTS `__PREFIX__mailchimp_subscribers` (
        `subscriberID` int(11) NOT NULL AUTO_INCREMENT,
        `subscriberEmail` varchar(255) NOT NULL DEFAULT '',
        `subscriberDate` datetime DEFAULT NULL,
        PRIMARY KEY (`subscriberID`)
      ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
      
      CREATE TABLE IF NOT EXISTS `__PREFIX__mailchimp_campaigns` (
          `campaignID` int(11) NOT NULL AUTO_INCREMENT,
          `campaignCID` char(64) NOT NULL,
          `campaignWebID` int(11) NOT NULL,
          `campaignCreateTime` datetime,
          `campaignSendTime` datetime,
          `campaignSent` int(11),
          `campaignSubject` varchar(255) NOT NULL DEFAULT '',
          `campaignArchiveURL` varchar(255) NOT NULL DEFAULT '',
          `campaignTitle` varchar(255) NOT NULL DEFAULT '',
          `campaignText` text,
          `campaignHTML` text,
          `campaignSlug` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`campaignID`),
          UNIQUE KEY `idx_cid` (`campaignCID`),
          KEY `idx_slug` (`campaignSlug`),
          FULLTEXT KEY `idx_search` (`campaignSubject`,`campaignHTML`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

      CREATE TABLE IF NOT EXISTS `__PREFIX__mailchimp_log` (
          `logID` int(11) NOT NULL AUTO_INCREMENT,
          `logEvent` varchar(255),
          `logDate` datetime,
          `logType` varchar(255),
          PRIMARY KEY (`logID`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    ";
        
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }

       
    $Settings = $API->get('Settings');
    $secret = $Settings->get('perch_mailchimp_secret')->val();   
    if (!$secret) {
      $Settings->set('perch_mailchimp_secret', md5(uniqid()));   
    }
     

    $API = new PerchAPI(1.0, 'perch_mailchimp');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_mailchimp', 'Show MailChimp on dashboard');
    $UserPrivileges->create_privilege('perch_mailchimp.data', 'Data import');
    return $result;

?>