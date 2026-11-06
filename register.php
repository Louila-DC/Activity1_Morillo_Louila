<?php
require_once "config.php";

function h($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$msgs = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $uname = trim($_POST["uname"]);
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    if (empty($uname) || empty($password) || empty($password2)) {
        $msgs[] = "All fields are required!";
    }
    elseif ($password !== $password2) {
        $msgs[] = "Passwords do not match!";
    }
    else {

        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $uname);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $msgs[] = "Username already exists!";
        }
        else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users(username, password) VALUES(?, ?)");
            $stmt->bind_param("ss", $uname, $hashedPassword);

            if ($stmt->execute()) {
                $msgs[] = "Registration successful!";
            } else {
                $msgs[] = "Error: " . $conn->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

    <h1>Inventory System</h1>
    <h2>Create your Account</h2>

    <br>

    <?php foreach ($msgs as $m): ?>
        <p style="color:green;"><?php echo h($m); ?></p>
    <?php endforeach; ?>

    <form action="" method="post">

        <label>Username:</label>
        <input type="text"
               name="uname"
               placeholder="Enter username"
               required
               autofocus>

        <br><br>

        <label>Password:</label>
        <input type="password"
               name="password"
               placeholder="Enter password"
               required>

        <br><br>

        <label>Re-enter Password:</label>
        <input type="password"
               name="password2"
               placeholder="Re-enter password"
               required>

        <br><br>

        <input type="submit" value="Register">

    </form>

    <br>

    <a href="login.php">Already have an account? Login here</a>

</body>
</html>