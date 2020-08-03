<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if user is logged
    verify_user_logged("user_id");

    // Checks for get variable
    verify_get_email("user_id");

    // Get user details
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
    $query = "SELECT *
        FROM users
        WHERE user_id = '{$user_id}'
        AND is_deleted = 0
        LIMIT 1;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "users.php");
    if (mysqli_num_rows($result) == 1){
        // Valid user
        $user = mysqli_fetch_assoc($result);

        $first_name = $user["first_name"];
        $last_name = $user["last_name"];
        $email = $user["email"];

    } else {
        exit(header("Location: users.php?err=user_not_found"));
    }
    
    // If submit modified form
    if (isset($_POST["submit"])) {
        $user_updated = false;
        $errors = array();

        // Check for required fields
        $required_fields = array("user_id", "first_name", "email");
        $errors = array_merge($errors, check_required_fields($required_fields));
        
        // Checks max length
        $field_max_length = array("first_name" => 50, "last_name" => 100, "email" => 100);
        $errors = array_merge($errors, check_field_max_length($field_max_length));
        
        // Validate email
        if (!is_email($_POST["email"])) {
            $errors[] = "Invalid Email";
        }
        
        // If not errors in form
        if (empty($errors)) {
            // Assign data in variables secure way
            $user_id = mysqli_real_escape_string($connection, $_POST["user_id"]);
            $first_name_new = mysqli_real_escape_string($connection, trim($_POST["first_name"]));
            $last_name_new = mysqli_real_escape_string($connection, trim($_POST["last_name"]));
            $email_new = mysqli_real_escape_string($connection, trim($_POST["email"]));

            if ($first_name_new != $first_name || $last_name_new != $last_name || $email_new != $email) {
                $first_name = $first_name_new;
                $last_name = $last_name_new;
                $email = $email_new;

                // Check for existance of email
                $query = "SELECT *
                    FROM users
                    WHERE email = '{$email}'
                    AND user_id != '{$user_id}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);

                // Verify query
                verify_query($result, "users.php");

                if (mysqli_num_rows($result) == 1) {
                    $errors[] = "Email already exists";

                } else {
                    $query = "UPDATE users SET ";
                    $query .= "first_name = '{$first_name}',";
                    $query .= "last_name = '{$last_name}',";
                    $query .= "email = '{$email}'";
                    $query .= "WHERE user_id = '{$user_id}' LIMIT 1;";

                    $result = mysqli_query($connection, $query);

                    // Verify query
                    verify_query($result, "users.php");
                    $user_updated = true;
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View/Modify User - UMS</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>

    <main>
        <h1>User: <?php echo "{$first_name} {$last_name}" ; ?></h1>

        <form action="modify_user.php?user_id=<?php echo $user_id; ?>" method="post">
            <fieldset>
                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_errors($errors);
                    }
                ?>

                <?php
                    // Check if User Modified successfully
                    if (isset($user_updated) && $user_updated) {
                        $_POST = array();
                        echo "<p class=\"info\">Modified User Successfull </p>";
                    }
                ?>

                <?php
                    // Check if changed password successfully
                    if (isset($_GET["password_changed"])) {
                        $_POST = array();
                        echo "<p class=\"info\">Password Changed </p>";
                    }
                ?>

                    <input type="hidden" name="user_id" value=<?php echo "'{$user_id}'"; ?>>

                <p>
                    <label for="first_name">First Name: </label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" value=<?php echo "'{$first_name}'"; ?> 
                        required>
                </p>

                <p>
                    <label for="last_name">Last Name: </label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" value=<?php echo "'{$last_name}'"; ?>>
                </p>

                <p>
                    <label for="email">Email Address: </label>
                    <input type="email" name="email" id="email" placeholder="Email Address" value=<?php echo "'{$email}'"; ?> 
                        required>
                </p>

                <p>
                    <label for="">Password: </label>
                    <a class="change_password" href="change_password.php?user_id=<?php echo $user_id; ?>">Change Password</a>
                        <!-- change_password -->
                </p>

                <p>
                    <button type="submit" name="submit">Save Changes</button>
                    <a class="cancel" href="users.php">Cancel</a> <!-- cancel -->
                </p>
            </fieldset>
        </form>
    </main>
</body>
</html>

<?php mysqli_close($connection); ?>