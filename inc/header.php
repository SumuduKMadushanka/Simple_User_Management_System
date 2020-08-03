<header class="clearfix">
        <div class="web_site_name">User Management System</div> <!-- web_site_name -->
        
        <div class="login_user">
            <div class="user_name">
                Welcome
                <?php
                    $username = $_SESSION["first_name"] . " " . $_SESSION["last_name"];
                    echo $username;
                ?>!
            </div> <!-- user_name -->
            <a class="cancel" href="logout.php">Log Out</a> <!-- cancel -->
        </div> <!-- login_user -->
</header>