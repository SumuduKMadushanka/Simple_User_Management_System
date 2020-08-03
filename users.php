<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if user is logged
    verify_user_logged("user_id");

    // Create users table
    $user_list = "";

    // Query for get users details
    $query = "SELECT *
        FROM users
        WHERE is_deleted = 0
        ORDER BY first_name;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result);
    
    while ($user = mysqli_fetch_assoc($result)) {
        if ($user["user_id"] == $_SESSION["user_id"]){
            $user_list .= "<tr class=\"user_row\">";
        } else{
            $user_list .= "<tr>";
        }
        $user_list .= "<td>{$user['first_name']}</td>";
        $user_list .= "<td>{$user['last_name']}</td>";
        $user_list .= "<td>{$user['last_login']}</td>";
        $user_list .= "<td><a href=\"modify_user.php?user_id={$user['user_id']}\">Edit</a></td>";
        $user_list .= "<td><a href=\"delete_user.php?user_id={$user['user_id']}\" 
            onclick=\"return confirm('Are you sure you want to delete {$user['email']}?');\">Delete</a></td>";
        $user_list .= "</tr>";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management System</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>

    <main>
        <h1>User List <span><a href="add_user.php">+ Add User</a></span></h1>

        <?php
            // Check for errors
            if (isset($_GET["err"])) {
                display_single_error($_GET["err"]);
            }
        ?>

        <?php
            // Check if User Added successfully
            if (isset($_GET["user_added"])) {
                echo "<p class=\"info\">Add User {$_GET["user_added"]} is Successfull </p>";
            }
        ?>

        <?php
            // Check if Delete a user
            if (isset($_GET["deleted"])) {
                echo "<p class=\"info\">User {$_GET["deleted"]} is Deleted </p>";
            }
        ?>

        <table class="main_table">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Last Login</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>

            <?php echo $user_list; ?>

        </table> <!-- main_table -->
    </main>
</body>
</html>

<?php mysqli_close($connection); ?>