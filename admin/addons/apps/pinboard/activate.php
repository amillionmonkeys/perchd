<?php
    include('../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'pinboard');
    $Lang = $API->get('Lang');