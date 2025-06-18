<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;y
    }
    .registration-card {
      width: 100%;
      max-width: 700px; /* Updated width */
      padding: 2rem;
      border-radius: 1rem;
      background-color: #fff;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="registration-card">
      <h4 class="mb-4 text-center">Registration Form</h4>
      <form action="ManagerDash.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email ID</label>
          <input type="email" class="form-control" id="email" name="email" required />
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" required />
        </div>
        <button type="submit" class="btn btn-primary w-100" href="ManagerDash.php">Register</button>
        <p class="text-center mt-3">
            Already have an account? 
            <a href="Login.php" class="text-decoration-none">Login here</a>
        </p>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
