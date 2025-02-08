<?php
require_once 'database_connect.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL query
    $sql = 'INSERT INTO users (username, password) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error); // Debugging output
    }

    // Bind parameters and execute
    $stmt->bind_param('ss', $username, $hashed_password);

    if ($stmt->execute()) {
        echo "User registered successfully";
    } else {
        echo "Error in registering user: " . $stmt->error; // Debugging output
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="register.php" method="POST">
        Enter new username: <br>
        <input type="text" name="username" required>
        <br><br>
        Enter password: <br>
        <input type="password" name="password" required>
        <br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
