<?php
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('listing')) {
	    $this->register_app('listing', 'Listing', 2, 'A listing for use with the Members App', '1.0');
	}
