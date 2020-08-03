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

    // If submit change password form
    if (isset($_POST["submit"])) {
        $errors = array();

        // Check for required fields
        $required_fields = array("user_id", "password", "confirm_password");
        $errors = array_merge($errors, check_required_fields($required_fields));

        // Confirms enter password twice correctly
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $errors[] = "Password and Confirm Password must match";
        }

        // If not errors in form
        if (empty($errors)) {
            $password = mysqli_real_escape_string($connection, $_POST["password"]);
            $salt = $user["salt"];
            $hashed_password = sha1($password . strval($salt));

            if ($hashed_password == $user["hashed_password"]) {
                $errors[] = "Password already used";

            } else {
                $query = "UPDATE users
                    SET hashed_password = '{$hashed_password}'
                    WHERE user_id = '{$user_id}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                
                // Verify query
                verify_query($result, "users.php");
                exit(header("Location: modify_user.php?user_id={$user_id}&password_changed=yes"));

            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - UMS</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>

    <main>
        <h1>User: <?php echo "{$first_name} {$last_name}" ; ?></h1>

        <form action="change_password.php?user_id=<?php echo $user_id; ?>" method="post">
            <fieldset>
                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_errors($errors);
                    }
                ?>

                <input type="hidden" name="user_id" value=<?php echo "'{$user_id}'"; ?>>

                <p>
                    <label for="first_name">First Name: </label>
                    <input type="text" name="first_name" id="first_name" value=<?php echo "'{$first_name}'"; ?> 
                        disabled>
                </p>

                <p>
                    <label for="last_name">Last Name: </label>
                    <input type="text" name="last_name" id="last_name" value=<?php echo "'{$last_name}'"; ?>
                        disabled>
                </p>

                <p>
                    <label for="email">Email Address: </label>
                    <input type="email" name="email" id="email" value=<?php echo "'{$email}'"; ?> 
                        disabled>
                </p>

                <p>
                    <label for="password">New Password: </label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </p>

                <p class="checkbox">
                    <label for="showpassword">Show Password: </label>
                    <input type="checkbox" name="showpassword" id="showpassword">
                </p>

                <p>
                    <label for="confirm_password">Confirm Password: </label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </p>

                <p>
                    <button type="submit" name="submit">Save Changes</button>
                    <a class="cancel" href="modify_user.php?user_id=<?php echo $user_id; ?>">Cancel</a> <!-- cancel -->
                </p>
            </fieldset>
        </form>
    </main>

    <script src="js/jquery.js"></script>
    <script>
        $(document).ready(function() {
            $('#showpassword').click(function() {
                if ($('#showpassword').is(':checked')) {
                    $('#password').attr('type', 'text');
                } else{
                    $('#password').attr('type', 'password');
                }
            });
        });
    </script>
</body>
</html>

<?php mysqli_close($connection); ?>