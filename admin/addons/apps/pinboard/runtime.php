<?php

	include ('lib/vendor/kijin/pinboard-api/pinboard-api.php');


    function get_tags(){

        $API  = new PerchAPI(1.0, 'pinboard');

        $Settings = $API->get('Settings');

        $username = $Settings->get('pinboard_username')->settingValue();

        $password = $Settings->get('pinboard_password')->settingValue();

        $pinboard = new PinboardAPI($username, $password);

        $tags = $pinboard->get_tags();

        $result = [];

        foreach ($tags as $key => $value) {
            if(substr( $value->tag, 0, 6 ) === "perch_") $result[$key] = $value->tag;
        }

        return $result;
    }

	function pinboard_bookmarks($tag)
    {
        $API  = new PerchAPI(1.0, 'pinboard');

        $Settings = $API->get('Settings');

        $username = $Settings->get('pinboard_username')->settingValue();

        $password = $Settings->get('pinboard_password')->settingValue();

        $pinboard = new PinboardAPI($username, $password);

        $items = $pinboard->search_by_tag($tag);

        $result = [];

        foreach ($items as $key=>$value) {
        	$result[$key] = array (
        		'title'=>$value->title,
        		'description'=>$value->description,
        		'url'=>$value->url,
                'date'=>gmdate("l dS F Y",$value->timestamp)
        	);
        }
        return $result;
    }
    