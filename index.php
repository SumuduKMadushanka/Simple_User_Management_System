<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if click Log IN button
    if (isset($_POST["submit"])) {
        $errors = array();

        // Check if username and password are correctly entered
        $required_fields = array("email", "password");
        $errors = array_merge($errors, check_required_fields($required_fields));

        // Validate Email
        if (!is_email($_POST["email"])) {
            $errors[] = "Invalid Email";
        }

        // Check are there any errors
        if (empty($errors)) {
            // save username and password in variables
            $email = mysqli_real_escape_string($connection, $_POST["email"]);
            $password = mysqli_real_escape_string($connection, $_POST["password"]);
            
            // Query create
            $query = "SELECT *
                FROM users
                WHERE email = '{$email}'
                AND is_deleted = 0
                LIMIT 1;";
            $result = mysqli_query($connection, $query);

            // Verify query
            verify_query($result);

            // Verify User
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                $hashed_password = sha1($password . strval($user['salt']));

                // Check if username and password are correct
                if ($user['email'] == $email && $user['hashed_password'] == $hashed_password) {
                    // Valid user found
                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["first_name"] = $user["first_name"];
                    $_SESSION["last_name"] = $user["last_name"];
                    
                    // Updating last Login
                    $query = "UPDATE users
                        SET last_login = NOW()
                        WHERE email = '{$email}'
                        LIMIT 1;";
                    $result = mysqli_query($connection, $query);

                    // Verify query
                    verify_query($result);

                    // Redirect to users.php page
                    exit(header("Location: users.php"));

                } else {
                    $errors[] = "Invalid username / pasword";

                }
            } else {
                $errors[] = "Invalid username / pasword";

            }
        }
    } else {
        clear_session();

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In - UMS</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="login">
        <form action="index.php" method="post">
            <fieldset>
                <legend><h1>Log In</h1></legend>

                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        clear_session();
                        echo "<p class=\"error\">Invalid Username / Password </p>";
                    }
                ?>

                <?php
                    // Check if Log Out user
                    if (isset($_GET["logout"])) {
                        echo "<p class=\"info\">Logout Successfull </p>";
                    }
                ?>

                <p>
                    <label for="email">Username: </label>
                    <input type="email" name="email" id="email" placeholder="Email Address">
                </p>
                
                <p>
                    <label for="password">Password: </label>
                    <input type="password" name="password" id="password" placeholder="Password">
                </p>

                <p>
                    <button type="submit" name="submit">Log In</button>
                </p>
            </fieldset>
        </form>
    </div> <!-- login -->
</body>
</html>

<?php mysqli_close($connection); ?>