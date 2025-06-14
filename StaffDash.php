<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "inventory_project";
$port = 3307;

$conn = new mysqli($host, $user, $password, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Form Submission
if (isset($_POST['submit'])) {
    $name = $_POST['Name'] ?? '';
    $price = $_POST['Price'] ?? '';
    $count = $_POST['Count'] ?? '';
    $product_id = $_POST['product_id'] ?? '';

    $image = $_FILES['image']['name'];
    $image_path = '';

    if (!empty($image)) {
        $target = "Product uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image_path = $image;
    }

    if (!empty($product_id)) {
        // Update existing product
        if (!empty($image_path)) {
            $stmt = $conn->prepare("UPDATE staff_add_product SET product_name=?, price=?, count=?, image=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $price, $count, $image_path, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE staff_add_product SET product_name=?, price=?, count=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $price, $count, $product_id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating product: " . $stmt->error . "');</script>";
        }
    } else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO staff_add_product (product_name, price, count, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $price, $count, $image_path);

        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully');</script>";
        } else {
            echo "<script>alert('Error adding product: " . $stmt->error . "');</script>";
        }
    }

    $stmt->close();
}

// Fetch data from students table
$sql = "SELECT * FROM staff_add_product ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container py-4">
    <h2 class="text-center mb-4">Staff Dashboard</h2>

    <div class="text-center mb-3">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        Add New Product
      </button>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="productForm" action="StaffDash.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Hidden input for edit -->
            <input type="hidden" name="product_id" id="productId" />
          <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="productName" name="Name" required />
          </div>
          <div class="mb-3">
            <label for="productPrice" class="form-label">Product Price</label>
            <input type="number" class="form-control" id="productPrice" name="Price" min="0" step="0.01" required />
          </div>
          <div class="mb-3">
            <label for="productCount" class="form-label">Product Count</label>
            <input type="number" class="form-control" id="productCount" name="Count" min="0" required />
          </div>
          <div class="mb-3">
            <label for="productImage" class="form-label">Product Image</label>
            <input type="file" class="form-control" id="productImage" name="image" accept="image/*" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" name="submit">Add Product</button>
        </div>
      </form>
    </div>
  </div>


  <div class="container">
  <div class="row gx-1 gy-1 justify-content-start">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col-sm-6 col-md-4 col-lg-3 mb-2 gx-1 gy-1">
        <div class="card" style="max-width: 250px; margin: start;">
          <?php if (!empty($row['image'])): ?>
            <img src="Product uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                 class="card-img-top" alt="Product Image" 
                 style="height: 150px; object-fit: cover;">
          <?php else: ?>
            <img src="https://via.placeholder.com/250x150?text=No+Image" 
                 class="card-img-top" alt="No Image">
          <?php endif; ?>
          <div class="card-body p-2">
            <h6 class="card-title mb-1" style="font-size: 1rem;">
              <?php echo htmlspecialchars($row['product_name']); ?>
            </h6>
            <p class="card-text mb-0" style="font-size: 0.9rem;">
              Price: ₹<?php echo number_format($row['price'], 2); ?><br>
              Count: <?php echo htmlspecialchars($row['count']); ?>
            </p>
            <!-- Edit Button -->
            <button type="button"
                    class="btn btn-sm btn-warning mt-2 edit-btn"
                    data-id="<?php echo $row['id']; ?>"
                    data-name="<?php echo htmlspecialchars($row['product_name']); ?>"
                    data-price="<?php echo $row['price']; ?>"
                    data-count="<?php echo $row['count']; ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#addProductModal">
              Edit
            </button>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('addProductModalLabel').textContent = 'Edit Product';
      document.getElementById('productName').value = this.dataset.name;
      document.getElementById('productPrice').value = this.dataset.price;
      document.getElementById('productCount').value = this.dataset.count;
      document.getElementById('productId').value = this.dataset.id;
      document.querySelector('button[name="submit"]').textContent = 'Update Product';
    });
  });

  // Reset form when modal is hidden
  document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('addProductModalLabel').textContent = 'Add New Product';
    document.querySelector('button[name="submit"]').textContent = 'Add Product';
  });
</script>


</body>
</html>
