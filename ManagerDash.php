<?php
session_start();
$host = 'sql111.infinityfree.com';
$user = 'if0_39247692';
$password = '4UGwXKXVavDgAA'; // no password
$database = 'if0_39247692_inventoryproject';
$port = 3306;

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Form Submission
if (isset($_POST['submit'])) {
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    $joining_date = $_POST['joining_date'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // File upload
    $profile = $_FILES['profile']['name'];
    $target = "uploads/" . basename($profile);
    move_uploaded_file($_FILES['profile']['tmp_name'], $target);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO manager_add_staff (type, name, joining_date, address, phone, profile, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $type, $name, $joining_date, $address, $phone, $profile, $username, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Employee added successfully');</script>";
    } else {
        echo "<script>alert('Error adding employee: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}


// Fetch data from manager table
$sql = "SELECT * FROM manager_add_staff ORDER BY id DESC";
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manager Dashboard with Modal Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .btn-custom {
      width: 48%;
      transition: background-color 0.3s ease;
    }
    .btn-add-staff:hover {
      background-color: #198754;
      color: white;
    }
    .btn-add-user:hover {
      background-color: #0d6efd;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <h2 class="mb-4 text-center">Manager Dashboard</h2>

    <div class="d-flex justify-content-center">
      <button type="button" class="btn btn-success btn-custom btn-add-staff order-2" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        Add New Employee
      </button>
    </div>
  </div>

  <!-- Modal for Add New Staff -->
  <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="managerdash.php" method="POST" class="modal-content" id="staffForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Add New Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" required>
              <option value="" disabled selected>Select Type</option>
              <option value="user">User</option>
              <option value="staff">Staff</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Joining Date</label>
            <input type="date" class="form-control" name="joining_date" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="tel" class="form-control" name="phone" pattern="[0-9]{10}" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Profile</label>
            <input type="file" class="form-control" name="profile" accept="image/*" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" name="submit">Add employee</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Dropdown Filter + Add New Dropdown -->
  <div class="d-flex justify-content-end my-3 mx-3">
    <div>
      <select id="filterSelect" class="form-select w-auto">
        <option value="all">All</option>
        <option value="user">User</option>
        <option value="staff">Staff</option>
      </select>
    </div>
  </div>

  <div class="container mt-5">
  <h4 class="text-center mb-3">Employee Details</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Employee Type</th>
          <th>Joining Date</th>
          <th>Address</th>
          <th>Phone</th>
          <th>Profile</th>
          <th>Username</th>
          <th>Password</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['type']) ?></td>
              <td><?= htmlspecialchars($row['joining_date']) ?></td>
              <td><?= htmlspecialchars($row['address']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td>
                <?php if (!empty($row['profile']) && file_exists("uploads/" . $row['profile'])): ?>
                  <img src="uploads/<?= htmlspecialchars($row['profile']) ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
                <?php else: ?>
                  <span class="text-muted">No Image</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['password']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center">No data available</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById("filterSelect").addEventListener("change", function () {
    const selectedType = this.value;
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
      const typeCell = row.querySelector("td:nth-child(3)"); // 3rd column = 'Employee Type'
      const typeText = typeCell ? typeCell.textContent.trim().toLowerCase() : "";

      if (selectedType === "all" || typeText === selectedType.toLowerCase()) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
</script>


</body>
</html>
