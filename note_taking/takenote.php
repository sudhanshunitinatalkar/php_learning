<?php
session_start();
require_once "database_connect.php"; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$message = ""; // Feedback message for user

// Handle form submission for creating a note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO userdata (user_id, title, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("iss", $user_id, $title, $content);
            if ($stmt->execute()) {
                header("Location: takenote.php?success=1"); // Redirect to prevent resubmission
                exit();
            } else {
                $message = "Error saving note: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Database error: " . $conn->error;
        }
    } else {
        $message = "Title and content cannot be empty.";
    }
}

// Handle note deletion
if (isset($_GET['delete'])) {
    $note_id = (int) $_GET['delete']; // Ensure it's an integer for security
    $sql = "DELETE FROM userdata WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $note_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: takenote.php");
    exit();
}

// Fetch user's notes
$sql = "SELECT id, title, content, created_at FROM userdata WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Notes</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">Note saved successfully!</p>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <h2>Create a New Note</h2>
    <form action="takenote.php" method="POST">
        Title: <input type="text" name="title" required><br><br>
        Content: <textarea name="content" required></textarea><br><br>
        <button type="submit" name="create">Save Note</button>
    </form>
    <hr>
    
    <h2>Your Notes</h2>
    <?php foreach ($notes as $note): ?>
        <div>
            <h3><?php echo htmlspecialchars($note['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
            <small>Created on: <?php echo $note['created_at']; ?></small><br>
            <a href="takenote.php?delete=<?php echo $note['id']; ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
        </div>
        <hr>
    <?php endforeach; ?>
    
    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
