<?php

class Listing extends PerchAPI_Base
{
	protected $table  = 'listings';
    protected $pk     = 'listingID';


    public function set_status($status)
    {

        $API = new PerchAPI(1, 'listing');
        $data = array('listingStatus'=>$status);
        $this->update($data);
    }


    public function to_array()
    {
        $out = parent::to_array();

        if ($out['listingDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['listingDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }

        return $out;
    }

}
