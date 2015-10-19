<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
        CREATE TABLE `__PREFIX__members` (
          `memberID` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `memberAuthType` char(32) NOT NULL DEFAULT 'native',
          `memberAuthID` char(64) NOT NULL DEFAULT '',
          `memberEmail` char(255) NOT NULL DEFAULT '',
          `memberPassword` char(255) NOT NULL DEFAULT '',
          `memberStatus` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
          `memberCreated` datetime NOT NULL DEFAULT '2013-01-01 00:00:00',
          `memberExpires` datetime DEFAULT NULL,
          `memberProperties` text NOT NULL,
          PRIMARY KEY (`memberID`),
          KEY `idx_email` (`memberEmail`),
          KEY `idx_type` (`memberAuthType`),
          KEY `idx_active` (`memberStatus`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `__PREFIX__members_forms` (
          `formID` int(10) NOT NULL AUTO_INCREMENT,
          `formKey` char(64) NOT NULL DEFAULT '',
          `formTitle` varchar(255) NOT NULL,
          `formSettings` text NOT NULL,
          PRIMARY KEY (`formID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `__PREFIX__members_member_tags` (
          `memberID` int(10) NOT NULL,
          `tagID` int(10) NOT NULL,
          `tagExpires` datetime DEFAULT NULL,
          PRIMARY KEY (`memberID`,`tagID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `__PREFIX__members_sessions` (
          `sessionID` char(40) NOT NULL DEFAULT '',
          `sessionExpires` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
          `sessionHttpFootprint` char(40) NOT NULL DEFAULT '',
          `memberID` int(10) unsigned NOT NULL DEFAULT '0',
          `sessionData` text NOT NULL,
          PRIMARY KEY (`sessionID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `__PREFIX__members_tags` (
          `tagID` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `tag` char(64) NOT NULL DEFAULT '',
          `tagDisplay` char(64) NOT NULL DEFAULT '',
          PRIMARY KEY (`tagID`),
          KEY `idx_tag` (`tag`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
    ";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }
 
   
    $API = new PerchAPI(1.0, 'perch_members');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_members', 'Manage members');

    
        
    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;

