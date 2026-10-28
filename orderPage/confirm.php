<?php
session_start();
require_once('../../project/db.php');
require_once('../../project/headerPage/header.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['last_order_id'])) {
    header('Location: ../shopPage/shop.php');
    exit;
}

$user = $_SESSION['user'];
$order_id = $_SESSION['last_order_id'];

// Fetch order from DB
$stmt = $conn->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $order_id, $user['id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<p style='text-align:center;padding-top: 132px;'>Order not found.</p>";
    exit;
}

$order_data = json_decode($order['order_data'], true);
if (!is_array($order_data)) $order_data = [];
// normalize payload: may include cart and gift
$cart_items = $order_data;
$gift_info = null;
if (isset($order_data['cart'])) {
    $cart_items = $order_data['cart'];
    $gift_info = $order_data['gift'] ?? null;
}
// if we have gift IDs, fetch human-readable names
$gift_labels = [];
$giftCharge = 0;
if (!empty($gift_info)) {
    // helper to lookup by id
    function lookupItem($conn, $id) {
        $stmt = $conn->prepare('SELECT name,price FROM gift_items WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $r = $stmt->get_result();
        $row = $r->fetch_assoc();
        return $row ? $row : null;
    }
    if (!empty($gift_info['wrapper'])) {
        $row = lookupItem($conn, $gift_info['wrapper']);
        if ($row) {
            $gift_labels['wrapper'] = $row['name'];
            $giftCharge += floatval($row['price']);
        }
    }
    if (!empty($gift_info['card'])) {
        $row = lookupItem($conn, $gift_info['card']);
        if ($row) {
            $gift_labels['card'] = $row['name'];
            $giftCharge += floatval($row['price']);
        }
    }
    if (!empty($gift_info['accessories'])) {
        foreach ($gift_info['accessories'] as $aid) {
            $row = lookupItem($conn, $aid);
            if ($row) {
                $gift_labels['accessories'][] = $row['name'];
                $giftCharge += floatval($row['price']);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed!</title>
    <link rel="stylesheet" href="../../project/cartPage/cart.css">
    <style>
        body {
        font-family: 'Mulish', Arial, sans-serif;
        background: #f8f8f8;
        margin: 0;
        padding: 0;
        }

        .order-confirmed-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* background: #f8f8f8; */
            padding: 104px 0;
        }
        .confirmation-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px #6f4670ff;
            padding: 45px 32px 55px 32px;
            max-width: 450px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        @media (max-width: 600px) {
            .confirmation-box {
            padding: 24px 10px;
            max-width: 95vw;
            }
        }
        .order-confirmed-container .checkmark {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 12px;
        }
        .order-confirmed-container h2 {
            margin-bottom: 14px;
            color: #222;
            font-size: 1.5em;
        }
        .order-confirmed-container p {
            color: #555;
            margin-bottom: 27px;
            font-size: 1em;
        }
        .order-summary {
            background: #f5f5f5;
            border-radius: 6px;
            padding: 17px 20px;
            margin-bottom: 18px;
            text-align: left;
            font-size: 0.98em;
            width: 90%;
        }
        .order-summary ul {
            padding-left: 18px;
            margin-bottom: 8px;
        }
        .order-summary li {
            margin-bottom: 4px;
        }
        .order-summary-total {
            font-weight: bold;
            color: #222;
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }
        .continue-btn {
            background: #362546ff;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 40px;
            width: 100%;
            font-size: 1em;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.6s;
            display: flex;
            justify-content: center;
        }
        .continue-btn:hover {
            background: #ab94c0ff;
            color: #000;
        }
    </style>
</head>
<body>
<div class="order-confirmed-container">
    <div class="confirmation-box">
    <div class="checkmark">✔️</div>
    <h2>Your Order is Confirmed!</h2>
    <p>
        Order <b>#<?php echo $order['id']; ?></b> is now part of our love story!<br>
        Cupid’s on the way with your special delivery. 💘
    </p>
    <div class="order-summary">
        <div style="margin-bottom:10px;"><b>Products:</b></div>
        <ul>
            <?php foreach ($cart_items as $item): ?>
                <li><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['qty']; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php if (!empty($gift_info)): ?>
            <div style="margin-top:8px;"><b>Gift options chosen:</b></div>
            <ul>
                <?php if (!empty($gift_labels['wrapper'])): ?>
                    <li>Wrapper: <?php echo htmlspecialchars($gift_labels['wrapper']); ?></li>
                <?php endif; ?>
                <?php if (!empty($gift_labels['card'])): ?>
                    <li>Card: <?php echo htmlspecialchars($gift_labels['card']); ?></li>
                <?php endif; ?>
                <?php if (!empty($gift_labels['accessories'])): ?>
                    <li>Accessories: <?php echo htmlspecialchars(implode(', ', array_filter($gift_labels['accessories']))); ?></li>
                <?php endif; ?>
                <?php if (!empty($gift_info['message'])): ?>
                    <li>Message: <?php echo htmlspecialchars($gift_info['message']); ?></li>
                <?php endif; ?>
            </ul>
            <div style="margin-top:6px;"><b>Gift charges:</b> ₹<?php echo number_format($giftCharge,2); ?></div>
        <?php endif; ?>
        <div class="order-summary-total"><span>Total:</span><span>₹<?php echo $order['total']; ?></span></div>
    </div>
    <form action="../shopPage/shop.php" method="get">
        <button class="continue-btn" type="submit">Continue Shopping</button>
    </form>
    </div>
</div>
</body>
</html>
<?php require_once('../../project/footerPage/footer.php'); ?>