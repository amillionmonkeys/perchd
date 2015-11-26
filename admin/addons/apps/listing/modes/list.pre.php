<?php
    
    $HTML = $API->get('HTML');
    
    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);
    
    $Listings = new Listings($API);


	$Form = $API->get('Form');

    if ($Form->posted() && $Form->validate()) {

        $listings = $Form->find_items('listing-', true);

        if (PerchUtil::count($listings)) {
            $status = $_POST['listingStatus'];
            foreach($listings as $listingID) {
                $Listing = $Listings->find($listingID);
                $Listing->set_status($status);
            }


        }

    }
	
	$pending_listing_count = $Listings->get_count('PENDING');

    $listings = array();
	
	$status = 'pending';

    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = $_GET['status'];
    }
    
    $listings = $Listings->get_by_status($status, $Paging);


    if ($listings == false) {

        $Listings->attempt_install();
        
    }
 
?>