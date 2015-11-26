<aside class="sidebar">

    <div class="widget widget--user">
        <div class="widget__header">
            <div class="widget__title"><h3><?php perch_layout('icons/icon--user')?> User</h3></div>
            
        </div>
        <div class="widget__content">
            <?php
                if (perch_member_logged_in()) {
            ?>  
                <ul>
                    <li><a href="/user/apps">My Apps</a></li>
                    <li><a href="/user/">Edit profile</a></li>
                    <li><a href="/user/logout">Log out</a></li>
                </ul>

            <?php
                }else{
                    perch_members_login_form(); 
                }
            ?>
        </div>
    </div>

</aside>