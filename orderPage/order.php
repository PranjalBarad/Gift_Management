<?php
session_start();
require_once('../../project/db.php');
require_once('../../project/headerPage/header.php');

if (!isset($_SESSION['user'])) {
  header('Location: ../logIn/log.php');
  exit;
}

$user = $_SESSION['user'];

// Fetch ALL orders for this user (newest first)
$stmt = $conn->prepare("SELECT id, total, created_at, status, order_data, phone, address FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($r = $result->fetch_assoc()) {
  $r['items'] = json_decode($r['order_data'], true) ?? [];
  $orders[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Orders</title>
  <link rel="stylesheet" href="order.css">
  <style>
    body { background: #f5f5f5; }
    .all-orders-container { max-width: 1000px; margin: 0 auto; padding: 0 20px; }
    .orders-title { font-size: 28px; font-weight: 600; color: #333; margin-bottom: 30px; }
    .no-orders { text-align: center; padding: 60px 20px; color: #666; }
    .no-orders p { font-size: 18px; margin-bottom: 20px; }
    .no-orders a { display: inline-block; background: #8b6f47; color: white; padding: 10px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; }
    .no-orders a:hover { background: #745a3a; }
    
    .order-card-item { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 24px; overflow: hidden; }
    .order-card-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .order-card-left h3 { margin: 0; color: #333; font-size: 18px; font-weight: 600; }
    .order-card-left p { margin: 4px 0 0 0; color: #666; font-size: 13px; }
    .order-card-right { display: flex; gap: 20px; align-items: center; }
    .order-price { text-align: right; }
    .order-price-amount { font-size: 22px; font-weight: 700; color: #333; }
    .status-badge { display: inline-block; padding: 6px 12px; border-radius: 6px; color: white; font-weight: 600; font-size: 12px; }
    .status-pending { background: #f59e0b; }
    .status-shipped { background: #3b82f6; }
    .status-delivered { background: #10b981; }
    
    .order-products-table { width: 100%; border-collapse: collapse; }
    .order-products-table th { background: #f8f9fa; padding: 12px 20px; text-align: left; font-weight: 600; color: #666; font-size: 13px; border-bottom: 2px solid #e9ecef; }
    .order-products-table td { padding: 12px 20px; border-bottom: 1px solid #f0f0f0; }
    .order-products-table tr:last-child td { border-bottom: none; }
    .product-colname { font-weight: 500; color: #333; }
    .product-colqty { text-align: center; color: #666; }
    .product-colprice { text-align: right; font-weight: 500; }
    
    .order-card-footer { background: #f8f9fa; padding: 16px 20px; display: flex; justify-content: flex-end; }
    .view-details-btn { background: var(--primary); color: white; border: none; padding: 10px 28px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s; font-size: 14px; }
    .view-details-btn:hover { background: var(--header-bg); }
    
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 0; }
    .modal-overlay.active { display: flex; align-items: center; justify-content: center; }
    .receipt-box { background: white; border-radius: 12px; width: 75%; height: 55vh; padding: 32px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow-y: auto; }
    .receipt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e9ecef; }
    .receipt-header-content h2 { margin: 0 0 8px 0; color: #333; font-size: 24px; }
    .receipt-header-content p { margin: 0; color: #666; font-size: 14px; }
    .receipt-close { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; padding: 0; width: 28px; height: 28px; }
    .receipt-close:hover { color: #333; }
    
    .receipt-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .receipt-table thead { border-bottom: 2px solid #e9ecef; }
    .receipt-table th { padding: 12px 0; text-align: left; font-weight: 600; color: #666; font-size: 13px; }
    .receipt-table td { padding: 12px 0; border-bottom: 1px solid #f0f0f0; color: #333; font-size: 14px; }
    .receipt-table tr:last-child td { border-bottom: none; }
    
    .receipt-summary { background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 24px; }
    .receipt-summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: #666; font-size: 14px; }
    .receipt-summary-row.total { border-top: 1px solid #e9ecef; padding-top: 8px; margin-top: 8px; color: #333; font-weight: 700; font-size: 16px; }
    
    .receipt-section { background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 16px; }
    .receipt-section h4 { margin: 0 0 12px 0; color: #333; font-size: 14px; font-weight: 600; }
    .receipt-section p { margin: 0; color: #666; font-size: 14px; line-height: 1.6; }
    
    .receipt-close-btn { width: 100%; background: var(--header-bg); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; margin-top: 24px; }
    .receipt-close-btn:hover { background: var(--primary); }
  </style>
</head>
<body>
<div class="all-orders-container">
  <h2 class="orders-title">Your Orders</h2>
  
  <?php if (empty($orders)): ?>
    <div class="no-orders">
      <p>You have no orders yet.</p>
      <a href="../shopPage/shop2.php">Start Shopping</a>
    </div>
  <?php else: ?>
    <?php foreach ($orders as $o): ?>
      <div class="order-card-item">
        <div class="order-card-header">
          <div class="order-card-left">
            <h3>Order #<?php echo $o['id']; ?></h3>
            <p>Placed: <?php echo date('Y-m-d H:i:s', strtotime($o['created_at'])); ?></p>
          </div>
          <div class="order-card-right">
            <div class="order-price">
              <div class="order-price-amount">₹<?php echo number_format($o['total'], 2); ?></div>
            </div>
            <span class="status-badge status-<?php echo strtolower($o['status'] ?? 'pending'); ?>">
              <?php echo htmlspecialchars($o['status'] ?? 'Pending'); ?>
            </span>
          </div>
        </div>
        
        <table class="order-products-table">
          <thead>
            <tr>
              <th>Product</th>
              <th class="product-colqty">Qty</th>
              <th class="product-colprice">Price</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($o['items'] as $item): ?>
              <tr>
                <td class="product-colname"><?php echo htmlspecialchars($item['name'] ?? 'Product'); ?></td>
                <td class="product-colqty"><?php echo $item['qty'] ?? 1; ?></td>
                <td class="product-colprice">₹<?php echo intval(str_replace('₹', '', $item['price'] ?? 0)) * ($item['qty'] ?? 1); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <div class="order-card-footer">
          <button class="view-details-btn" onclick="openReceipt(<?php echo $o['id']; ?>)">View Details</button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="modal-overlay">
  <div class="receipt-box">
    <div class="receipt-header">
      <div class="receipt-header-content">
        <h2 id="receiptOrderId">Order #0</h2>
        <p id="receiptOrderDate">0000-00-00 · Pending</p>
      </div>
      <button class="receipt-close" onclick="closeReceipt()">&times;</button>
    </div>
    
    <table class="receipt-table">
      <thead>
        <tr>
          <th>Product</th>
          <th style="text-align: center;">Qty</th>
          <th>Price</th>
          <th style="text-align: right;">Subtotal</th>
        </tr>
      </thead>
      <tbody id="receiptItems">
      </tbody>
    </table>
    
    <div class="receipt-summary">
      <div class="receipt-summary-row">
        <span>Items</span>
        <span id="receiptItemCount">0</span>
      </div>
      <div class="receipt-summary-row">
        <span>Gift charges</span>
        <span id="receiptGiftCharge">₹0</span>
      </div>
      <div class="receipt-summary-row total">
        <span>Total</span>
        <span id="receiptTotal">₹0</span>
      </div>
    </div>
    
    <div class="receipt-section">
      <h4>Status</h4>
      <p id="receiptStatus">Pending</p>
    </div>
    
    <div class="receipt-section">
      <h4>Payment</h4>
      <p>COD</p>
    </div>
    
    <div class="receipt-section">
      <h4>Delivery</h4>
      <p id="receiptAddress">Address not available</p>
      <p id="receiptPhone" style="margin-top: 8px;"></p>
    </div>
    <div id="receiptGiftSection" class="receipt-section" style="display:none;">
      <h4>Gift Options</h4>
      <div id="receiptGiftContent"></div>
    </div>
    
    <button class="receipt-close-btn" onclick="closeReceipt()">Close</button>
  </div>
</div>

<script>
  function openReceipt(orderId) {
    fetch('get_receipt.php?id=' + orderId)
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const order = data.order;
          
          document.getElementById('receiptOrderId').textContent = 'Order #' + order.id;
          document.getElementById('receiptOrderDate').textContent = order.created_at + ' · ' + order.status;
          
          let itemsHtml = '';
          let itemCount = 0;
          order.items.forEach(item => {
            const subtotal = parseInt(item.price.replace('₹', '')) * item.qty;
            itemCount += item.qty;
            itemsHtml += `
              <tr>
                <td>${item.name}</td>
                <td style="text-align: center;">${item.qty}</td>
                <td>${item.price}</td>
                <td style="text-align: right;">₹${subtotal}</td>
              </tr>
            `;
          });
          
          document.getElementById('receiptItems').innerHTML = itemsHtml;
          document.getElementById('receiptItemCount').textContent = itemCount;
          document.getElementById('receiptTotal').textContent = '₹' + order.total;
          // gift
          if (order.gift) {
            document.getElementById('receiptGiftSection').style.display = '';
            const g = order.gift;
            let html = '';
            if (g.wrapper) html += `<p><strong>Wrapper:</strong> ${g.wrapper.name} — ₹${parseFloat(g.wrapper.price).toFixed(2)}</p>`;
            if (g.card) html += `<p><strong>Card:</strong> ${g.card.name} — ₹${parseFloat(g.card.price).toFixed(2)}</p>`;
            if (g.accessories && g.accessories.length) {
              html += `<p><strong>Accessories:</strong> ${g.accessories.map(a=>a.name+' (₹'+parseFloat(a.price).toFixed(2)+')').join(', ')}</p>`;
            }
            if (g.message) html += `<p><strong>Message:</strong> ${g.message}</p>`;
            document.getElementById('receiptGiftContent').innerHTML = html;
            document.getElementById('receiptGiftCharge').textContent = '₹' + (order.gift_charge || '0.00');
          } else {
            document.getElementById('receiptGiftSection').style.display = 'none';
            document.getElementById('receiptGiftContent').innerHTML = '';
            document.getElementById('receiptGiftCharge').textContent = '₹0';
          }
          document.getElementById('receiptStatus').textContent = order.status;
          document.getElementById('receiptAddress').textContent = order.address || 'Address not available';
          document.getElementById('receiptPhone').textContent = order.phone ? 'Phone: ' + order.phone : '';
          
          document.getElementById('receiptModal').classList.add('active');
        } else {
          alert('Failed to load order details');
        }
      })
      .catch(err => alert('Error: ' + err));
  }
  
  function closeReceipt() {
    document.getElementById('receiptModal').classList.remove('active');
  }
  
  document.getElementById('receiptModal').addEventListener('click', function(e) {
    if (e.target === this) closeReceipt();
  });
</script>
</body>
</html>
<?php require_once('../../project/footerPage/footer.php'); ?>