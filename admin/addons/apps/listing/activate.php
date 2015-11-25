<?php

    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "

    CREATE TABLE IF NOT EXISTS `__PREFIX__listings` (
      `listingID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `memberID` int(10) NOT NULL,
      `listingDateTime` datetime NOT NULL,
      `listingType` text NOT NULL,
      `listingTitle` text NOT NULL,
      `listingSlug` text NOT NULL,
      `listingHTML` text NOT NULL,
      `listingStatus` enum('LIVE','PENDING','REJECTED') NOT NULL DEFAULT 'PENDING',
      `listingDynamicFields` text NOT NULL,
      PRIMARY KEY (`listingID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    ";

    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }


    $API = new PerchAPI(1.0, 'listing');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('listing', 'Access listings');
    $UserPrivileges->create_privilege('listing.moderate', 'Moderate listing');


    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);

    return $result;