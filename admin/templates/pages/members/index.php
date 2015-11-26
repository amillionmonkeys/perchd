<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<?php 
    $title = perch_pages_title(true);

    perch_layout('header', [
    	'title'=>$title . ' | perch.io'
    ]);
   
    // perch_layout('homepage-header'); 
?>      
        <div class="outer-container">
            <main class="main-body">
                <?php 
					if (perch_member_logged_in()) {
						echo '<h1>Welcome back, '.perch_member_get('first_name').'!</h1>';
                        perch_member_form('profile.html');
                        echo '<br />';
                        echo '<h2>Update your password</h2>';
                        perch_member_form('password.html');
					}else{
						echo '<h1>Hello!</h1>';
						echo '<p>You are not logged in. Please login or <a href="/user/register">register</a>.</p>';
					}
                    
				?>
            </main>
            <?php perch_layout('user-sidebar'); ?>
        </div>
        
    <?php perch_layout('footer'); ?>
