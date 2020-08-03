<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if user is logged
    verify_user_logged("user_id");

    // Checks for get variable
    verify_get_email("user_id");

    // Get user_id
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
    $query = "SELECT *
        FROM users
        WHERE user_id = '{$user_id}'
        LIMIT 1;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "users.php");
    if (mysqli_num_rows($result) != 1){
        exit(header("Location: users.php?err=user_not_found"));

    } else if ($user_id != $_SESSION["user_id"]) {
        $email = mysqli_fetch_assoc($result)["email"];

        $query = "UPDATE users
            SET is_deleted = 1
            WHERE user_id = '{$user_id}'
            LIMIT 1;";
        $result = mysqli_query($connection, $query);

        // Verify query
        verify_query($result, "users.php");
        exit(header("Location: users.php?deleted={$email}"));
        
    } else {
        exit(header("Location: users.php?err=cannot_delete_current_user"));

    }

?>

<?php mysqli_close($connection); ?>