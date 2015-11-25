<?php
    $HTML = $API->get('HTML');
	$Form = $API->get('Form');

    $message = false;

    $Listings = new Listings;

    if (!$CurrentUser->has_priv('listings.moderate')) {
        PerchUtil::redirect($API->app_path());
    }

    if (isset($_GET['id']) && $_GET['id']!='') {
        $listingID = (int) $_GET['id'];
        $Listing = $Listings->find($listingID);
        $details = $Listing->to_array();
    }else{
        $message = $HTML->failure_message('Sorry, that comment could not be found.');
    }


    $Template   = $API->get('Template');
    $Template->set('listings/listing.html', 'listing');
    $Form->handle_empty_block_generation($Template);
    $tags = $Template->find_all_tags_and_repeaters();

    $Form->set_required_fields_from_template($Template, $details);

     if ($Form->submitted()) {

        $fixed_fields = $Form->receive(array('listingID','memberID', 'listingHTML', 'listingStatus', 'listingDateTime', 'listingSlug', 'listingTitle'));
        $data = $Form->get_posted_content($Template, $Listings, $Listing);
        $data = array_merge($data, $fixed_fields);

        if ($Listing->listingStatus()!=$data['listingStatus']) {
            // status has changed

            $Listing->set_status($data['listingStatus']);
        }

        

        $Listing->update($data);

        if (is_object($Listing)) {
            $message = $HTML->success_message('The comment has been successfully edited.');
        }else{
            $message = $HTML->failure_message('Sorry, that comment could not be edited.');
        }

        if ($Form->submitted_with_add_another()) {
            // find the next unmoderated
            $NextListing = $Listing->get_first_pending($Listing->id());
            if ($NextListing) {
                PerchUtil::redirect($API->app_path().'/edit/?id='.$NextListing->id());
            }else{
                PerchUtil::redirect($API->app_path());
            }
        }



     }

     $details = $Listing->to_array();

?>