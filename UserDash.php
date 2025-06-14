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

// Handle product addition
if (isset($_POST['submit'])) {
    $name = $_POST['Name'] ?? '';
    $price = $_POST['Price'] ?? '';
    $count = $_POST['Count'] ?? '';

    $image = $_FILES['image']['name'];
    $target = "Product uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO staff_add_product (product_name, price, count, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $price, $count, $image);
    $stmt->execute();
    $stmt->close();
}

// Handle Finish button (reduce stock)
if (isset($_POST['finish'])) {
    $orders = json_decode($_POST['orders'], true);
    foreach ($orders as $order) {
        $stmt = $conn->prepare("UPDATE staff_add_product SET count = count - ? WHERE id = ?");
        $stmt->bind_param("ii", $order['quantity'], $order['id']);
        $stmt->execute();
        $stmt->close();
    }
    echo "<script>alert('Order completed and stock updated.'); window.location.href='StaffDash.php';</script>";
    exit;
}

$result = $conn->query("SELECT * FROM staff_add_product");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
  <h2 class="text-center mb-4">User Billing Dashboard</h2>

  <div class="mb-4">
    <input type="text" id="searchBar" class="form-control" placeholder="Search for a product..." />
  </div>

  <form id="orderForm" method="POST" action="StaffDash.php">
    <table class="table table-bordered text-center" id="productTable">
      <thead class="table-light">
        <tr>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Unit Price (₹)</th>
          <th>Total (₹)</th>
        </tr>
      </thead>
      <tbody id="productList">
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr data-name="<?php echo strtolower($row['product_name']); ?>">
          <td><?php echo htmlspecialchars($row['product_name']); ?></td>
          <td>
            <input type="number" class="form-control quantity-input" 
                   min="0" max="<?php echo (int)$row['count']; ?>" 
                   value="0"
                   data-price="<?php echo $row['price']; ?>" 
                   data-id="<?php echo $row['id']; ?>" 
                   style="width: 80px; margin: auto;" />
            <small>In stock: <?php echo (int)$row['count']; ?></small>
          </td>
          <td><?php echo number_format($row['price'], 2); ?></td>
          <td class="total-price">0.00</td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-between fw-bold mb-3">
      <div>Total Items: <span id="totalCount">0</span></div>
      <div>Total Bill: ₹<span id="totalBill">0.00</span></div>
    </div>

    <input type="hidden" name="orders" id="ordersInput" />
    <button type="button" id="previewBtn" class="btn btn-success w-50 fixed-bottom mx-auto mb-3">Finish</button>
  </form>
</div>

<!-- Bill Summary Modal -->
<div class="modal fade" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="billModalLabel">Order Summary</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="billSummary" style="white-space: pre-line;"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmBtn" class="btn btn-success">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
const quantityInputs = document.querySelectorAll('.quantity-input');
const totalCountEl = document.getElementById('totalCount');
const totalBillEl = document.getElementById('totalBill');
const ordersInput = document.getElementById('ordersInput');
const productRows = document.querySelectorAll('#productList tr');
const searchBar = document.getElementById('searchBar');
const orderForm = document.getElementById('orderForm');
const previewBtn = document.getElementById('previewBtn');
const confirmBtn = document.getElementById('confirmBtn');
const billSummary = document.getElementById('billSummary');

function updateTotals() {
  let totalCount = 0;
  let totalBill = 0;
  productRows.forEach(row => {
    const qtyInput = row.querySelector('.quantity-input');
    const price = parseFloat(qtyInput.dataset.price);
    let qty = parseInt(qtyInput.value) || 0;
    if (qty > parseInt(qtyInput.max)) {
      qty = parseInt(qtyInput.max);
      qtyInput.value = qty;
    }
    const totalPriceCell = row.querySelector('.total-price');
    const totalPrice = qty * price;
    totalPriceCell.textContent = totalPrice.toFixed(2);
    totalCount += qty;
    totalBill += totalPrice;
  });
  totalCountEl.textContent = totalCount;
  totalBillEl.textContent = totalBill.toFixed(2);
}

quantityInputs.forEach(input => input.addEventListener('input', updateTotals));

searchBar.addEventListener('input', () => {
  const filter = searchBar.value.toLowerCase();
  productRows.forEach(row => {
    const name = row.dataset.name;
    row.style.display = name.includes(filter) ? '' : 'none';
  });
});

previewBtn.addEventListener('click', () => {
  const orders = [];
  productRows.forEach(row => {
    const qtyInput = row.querySelector('.quantity-input');
    const qty = parseInt(qtyInput.value);
    if (qty > 0) {
      orders.push({
        id: qtyInput.dataset.id,
        name: row.cells[0].textContent,
        quantity: qty,
        price: parseFloat(qtyInput.dataset.price),
        total: qty * parseFloat(qtyInput.dataset.price)
      });
    }
  });

  if (orders.length === 0) {
    alert("Please select at least one product quantity before finishing.");
    return;
  }

  ordersInput.value = JSON.stringify(orders);

  let summary = "";
  orders.forEach(o => {
    summary += `${o.name} - Qty: ${o.quantity} - Total: ₹${o.total.toFixed(2)}\n`;
  });
  summary += `\nTotal Items: ${orders.reduce((a, b) => a + b.quantity, 0)}\n`;
  summary += `Total Bill: ₹${orders.reduce((a, b) => a + b.total, 0).toFixed(2)}`;

  billSummary.textContent = summary;
  const modal = new bootstrap.Modal(document.getElementById('billModal'));
  modal.show();
});

confirmBtn.addEventListener('click', () => {
  orderForm.submit();
});

updateTotals();
</script>

<script>
  document.getElementById("searchBar").addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll("#productTable tbody tr");

    rows.forEach(row => {
      const productName = row.getAttribute("data-name");
      if (productName.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
