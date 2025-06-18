<?php
session_start();
// DB Connection
$host = 'sql111.infinityfree.com';
$user = 'if0_39247692';
$password = '4UGwXKXVavDgAA'; // no password
$database = 'if0_39247692_inventoryproject';
$port = 3306;

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
// Handle Login
if (isset($_POST['log'])) {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    $sql = "SELECT * FROM manager_add_staff WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['user'] = $username;
        echo "<script>window.location.href='userdash.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-card">
      <h4 class="text-center mb-4">User Login</h4>
      <form action="userdash.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="user" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="pass" required />
        </div>
        <button type="submit" class="btn btn-primary w-100" name="log">Login</button><br><br>
        <button type="submit" class="btn w-100" style="background-color: #6c757d; color: white;">Cancel</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
