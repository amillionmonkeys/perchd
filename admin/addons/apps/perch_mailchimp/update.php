<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;


    $API = new PerchAPI(1.0, 'perch_mailchimp');



    $Settings = $API->get('Settings');

    if ($Settings->get('perch_mailchimp_update')->val()!='2.0') {

	    $db = $API->get('DB');

	    $api_key = $Settings->get('perch_mailchimp_api_key')->settingValue();
		$list_id = $Settings->get('perch_mailchimp_list_id')->settingValue();

	    $sql = 'SHOW TABLES LIKE "'.PERCH_DB_PREFIX.'mailchimp_campaigns"';
	    $result = $db->get_value($sql);
	    
	    if(!$result) {
		    // Let's go
		    $sql = "
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
		        if ($statement!='') $db->execute($statement);
		    }

			$db->execute($sql);

		    $secret = $Settings->get('perch_mailchimp_secret')->val();   
		    if (!$secret) {
		      $Settings->set('perch_mailchimp_secret', md5(uniqid()));   
		    }

		}

		$Settings->set('perch_mailchimp_update', '2.0');

	}