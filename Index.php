<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Cards</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .custom-card {
      min-height: 250px;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row g-4 w-100 justify-content-center">

      <!-- Admin Card -->
      <div class="col-md-3">
        <div class="card text-center shadow custom-card">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="card-title">Admin</h5>
              <p class="card-text">Access the admin dashboard.</p>
            </div>
            <a href="ManagerDash.php" class="btn btn-primary mt-3">Login</a>
          </div>
        </div>
      </div>

      <!-- Manager Card -->
      <div class="col-md-3">
        <div class="card text-center shadow custom-card">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="card-title">Manager</h5>
              <p class="card-text">Manage teams and resources.</p>
            </div>
            <a href="Register.php" class="btn btn-primary mt-3">Login</a>
          </div>
        </div>
      </div>

      <!-- Staff Card -->
      <div class="col-md-3">
        <div class="card text-center shadow custom-card">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="card-title">Staff</h5>
              <p class="card-text">Staff login portal.</p>
            </div>
            <a href="StaffLogin.php" class="btn btn-primary mt-3">Login</a>
          </div>
        </div>
      </div>

      <!-- User Card -->
      <div class="col-md-3">
        <div class="card text-center shadow custom-card">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="card-title">User</h5>
              <p class="card-text">Access your account.</p>
            </div>
            <a href="UserLogin.php" class="btn btn-primary mt-3">Login</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
