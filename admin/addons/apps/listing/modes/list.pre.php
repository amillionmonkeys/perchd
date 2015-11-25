<?php
    
    $HTML = $API->get('HTML');
    
    
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);
    
    $Listing = new Listings($API);


	$Form = $API->get('Form');

    if ($Form->posted() && $Form->validate()) {

        $listings = $Form->find_items('listing-', true);
        if (PerchUtil::count($listings)) {
            $status = $_POST['listingStatus'];
            foreach($listings as $listingID) {
                $Listing = $Listing->find($listingID);
                $Listing->set_status($status);
            }


        }

    }


	
	$pending_listing_count = $Listing->get_count('PENDING');

    $listings = array();
	
	$status = 'pending';

    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = $_GET['status'];
    }
    
    $listings = $Listing->get_by_status($status, $Paging);


    if ($listings == false) {

        $Listing->attempt_install();
        
    }
 
?>