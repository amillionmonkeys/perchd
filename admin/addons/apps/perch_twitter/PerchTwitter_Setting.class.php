<?php

class PerchTwitter_Setting extends PerchAPI_Base
{
    protected $table  = 'twitter_settings';
    protected $pk     = 'settingID';


    public function update($data)
    {
        
        // Update the data
        parent::update($data);

        
 		return true;
    }
    
    

}
