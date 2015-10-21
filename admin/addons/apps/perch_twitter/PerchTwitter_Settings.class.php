<?php

class PerchTwitter_Settings extends PerchAPI_Factory
{
    protected $table     = 'twitter_settings';
	protected $pk        = 'settingID';
	protected $singular_classname = 'PerchTwitter_Setting';
	
	protected $default_sort_column = 'settingID';
    
    /*
        Get the setting
    */
    public function find($id=false) 
    {
		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'twitter_settings LIMIT 1';
		
		$row = $this->db->get_row($sql);
		
		// monkey patch
		if (is_array($row)) {
			if (!array_key_exists('settingUpdateInterval', $row)) {
				$sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_settings` ADD `settingUpdateInterval` INT(10)  UNSIGNED  NOT NULL  DEFAULT \'0\'  AFTER `settingTwitterID`';
				$this->db->execute($sql);
				$row['settingUpdateInterval'] = 0;
			}

			if (!array_key_exists('settingTwitterKey', $row)) {

				$sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_settings` ADD `settingTwitterKey` VARCHAR(255)  NOT NULL  DEFAULT \'\'  AFTER `settingUpdateInterval`';
				$this->db->execute($sql);
				$row['settingTwitterKey'] = '';

				$sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_settings` ADD `settingTwitterSecret` VARCHAR(255)  NOT NULL  DEFAULT \'\'  AFTER `settingTwitterKey`';
				$this->db->execute($sql);
				$row['settingTwitterSecret'] = '';

			}

			if (!array_key_exists('settingTwitterToken', $row)) {

				$sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_settings` ADD `settingTwitterToken` VARCHAR(255)  NOT NULL  DEFAULT \'\'  AFTER `settingTwitterSecret`';
				$this->db->execute($sql);
				
			
				$sql = 'ALTER TABLE `'.PERCH_DB_PREFIX.'twitter_settings` ADD `settingTwitterTokenSecret` VARCHAR(255)  NOT NULL  DEFAULT \'\'  AFTER `settingTwitterToken`';
				$this->db->execute($sql);
				
			

			}
		}else{
			$this->db->execute('INSERT INTO `'.PERCH_DB_PREFIX.'twitter_settings` (settingTwitterID) VALUES (\'\')');
			return $this->find($id);
		}


		return $this->return_instance($row);
	}


    
}
