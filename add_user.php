<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if user is logged
    verify_user_logged("user_id");

    $first_name = "";
    $last_name = "";
    $email = "";

    if (isset($_POST["submit"])) {
        $errors = array();

        // Check for required fields
        $required_fields = array("first_name", "email", "password", "confirm_password");
        $errors = array_merge($errors, check_required_fields($required_fields));
        
        // Checks max length
        $field_max_length = array("first_name" => 50, "last_name" => 100, "email" => 100);
        $errors = array_merge($errors, check_field_max_length($field_max_length));

        // Assign data in variables secure way
        $first_name = mysqli_real_escape_string($connection, trim($_POST["first_name"]));
        $last_name = mysqli_real_escape_string($connection, trim($_POST["last_name"]));
        $email = mysqli_real_escape_string($connection, trim($_POST["email"]));

        // Validate email
        if (!is_email($_POST["email"])) {
            $errors[] = "Invalid Email";
        }

        // Confirms enter password twice correctly
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $errors[] = "Password and Confirm Password must match";
        }

        // If not errors in form
        if (empty($errors)){
            $password = mysqli_real_escape_string($connection, $_POST["password"]);
            $salt = rand();
            $hashed_password = sha1($password . strval($salt));

            // Check for user existence
            $query = "SELECT *
                FROM users
                WHERE email = '{$email}'
                LIMIT 1;";
            $result = mysqli_query($connection, $query);

            // Verify query
            verify_query($result);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                if ($user['is_deleted'] == 0) {
                    // User already exists
                    $errors[] = "Email already exists";

                } else {
                    // Existed user is deleted
                    $query = "UPDATE users
                        SET first_name = '{$first_name}',
                        last_name = '{$last_name}',
                        hashed_password = '{$hashed_password}',
                        salt = '{$salt}',
                        is_deleted = 0
                        WHERE email = '{$email}'
                        LIMIT 1;";
                    $result = mysqli_query($connection, $query);

                    // Verify query
                    verify_query($result, "users.php");
                    exit(header("Location: users.php?user_added={$email}"));
                }
            } else {
                // No user found
                $query = "INSERT INTO users(
                    first_name, last_name, email, hashed_password, salt)
                    VALUES(
                    '{$first_name}', '{$last_name}', '{$email}', '{$hashed_password}', {$salt});";
                $result = mysqli_query($connection, $query);

                // Verify query
                verify_query($result, "users.php");
                exit(header("Location: users.php?user_added={$email}"));
            }
        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User - UMS</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>

    <main>
        <h1>Add New User</h1>

        <form action="add_user.php" method="post">
            <fieldset>
                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_errors($errors);
                    }
                ?>

                <p>
                    <label for="first_name">First Name: </label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" required
                        value=<?php echo "'{$first_name}'"; ?>>
                </p>

                <p>
                    <label for="last_name">Last Name: </label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name"
                        value=<?php echo "'{$last_name}'"; ?>>
                </p>

                <p>
                    <label for="email">Email Address: </label>
                    <input type="email" name="email" id="email" placeholder="Email Address" required
                        value=<?php echo "'{$email}'"; ?>>
                </p>

                <p>
                    <label for="password">Password: </label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </p>
                
                <p>
                    <label for="confirm_password">Confirm Password: </label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </p>

                <p>
                    <button type="submit" name="submit">Save</button>
                    <a class="cancel" href="users.php">Cancel</a> <!-- cancel -->
                </p>
            </fieldset>
        </form>
    </main>
</body>
</html>

<?php mysqli_close($connection); ?>