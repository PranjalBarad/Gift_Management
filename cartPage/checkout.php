<?php
session_start();
require_once('../../project/db.php');
require_once('../../project/headerPage/header.php');

// Redirect if not logged in or cart is empty
if (!isset($_SESSION['user'])) {
  header('Location: ../logIn/log.php');
  exit;
}
if (empty($_SESSION['cart'])) {
  header('Location: ../shopPage/shop2.php');
  exit;
}

$user = $_SESSION['user'];
$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) {
  $subtotal += intval(str_replace('₹', '', $item['price'])) * $item['qty'];
}
$shipping = 0;
$total = $subtotal + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
  $fullname = $_POST['fullname'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $altphone = $_POST['altphone'] ?? '';
  $address = $_POST['address'] ?? '';
  // try decode gift JSON if provided
  $gift = null;
  if (isset($_POST['gift'])) {
    $gift = json_decode($_POST['gift'], true);
  }

  // calculate total including gift similarly to place_order.php
  $subtotal = 0;
  foreach ($cart as $item) {
    $subtotal += intval(str_replace('₹', '', $item['price'])) * $item['qty'];
  }
  $shipping = 0;
  $total = $subtotal + $shipping;
  if ($gift) {
    // simple DB lookup for prices
    function getGiftPrice($conn, $id) {
      $stmt = $conn->prepare('SELECT price FROM gift_items WHERE id=? LIMIT 1');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $r = $stmt->get_result();
      $row = $r->fetch_assoc();
      return $row ? floatval($row['price']) : 0;
    }
    if (!empty($gift['wrapper'])) $total += getGiftPrice($conn, $gift['wrapper']);
    if (!empty($gift['card'])) $total += getGiftPrice($conn, $gift['card']);
    if (!empty($gift['accessories']) && is_array($gift['accessories'])) {
      foreach ($gift['accessories'] as $aid) {
        $total += getGiftPrice($conn, $aid);
      }
    }
  }

  // Save order in DB (match schema: user_id, phone, altphone, address, order_data, total)
  $order_payload = ['cart' => $cart];
  if ($gift) $order_payload['gift'] = $gift;
  $order_data = json_encode($order_payload);
  $stmt = $conn->prepare("INSERT INTO orders (user_id, phone, altphone, address, order_data, total, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param("issssd", $user['id'], $phone, $altphone, $address, $order_data, $total);
  $stmt->execute();
  $order_id = $stmt->insert_id;

  // Clear cart
  unset($_SESSION['cart']);

  // Save order id in session for orderPage/order.php
  $_SESSION['last_order_id'] = $order_id;

  // Redirect to user's orders list
  header('Location: ../orderPage/order.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="checkout.css">
</head>

<body>
  <div class="checkout-container">
    <form class="checkout-form" method="post" id="checkout-form">
      <h2>Shipping details</h2>
      <label>Full Name *</label>
      <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
      <label>Phone Number *</label>
      <input type="text" name="phone" required>
      <label>Alternate Number</label>
      <input type="text" name="altphone">
      <label>Address *</label>
      <textarea name="address" rows="3" required></textarea>
      <!-- <button type="submit" name="make_payment">MAKE PAYMENT</button> -->
    </form>
    <div class="checkout-summary">
      <div class="summary-title">Bill Summary</div>
      <div class="summary-products">
        <ul>
          <?php foreach ($cart as $item): ?>
            <li><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['qty']; ?> <span
                style="float:right;">₹<?php echo intval(str_replace('₹', '', $item['price'])) * $item['qty']; ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="summary-row" id="subtotal"><span>Subtotal:</span><span>₹<?php echo $subtotal; ?></span></div>
      <div class="summary-row" id="gift-row" style="display:none;"><span>Gift:</span><span>₹0</span></div>
      <div class="summary-row"><span>Shipping:</span><span>₹<?php echo $shipping; ?></span></div>
      <div class="summary-row summary-total"><span>Total:</span><span>₹<?php echo $total; ?></span></div>
      <div class="pay-radio">
        <input type="radio" checked> Pay Online
      </div>
      <button type="submit" name="make_payment" form="checkout-form">MAKE PAYMENT</button>
    </div>
  </div>

  <!-- gift options modal -->
  <div id="giftModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:1000;">
    <div style="background:linear-gradient(135deg,#ffffff 0%, #f7f3ff 100%);width:70%;height:75%;position:fixed;left:15%;top:8%;border-radius:12px;box-shadow:0 30px 80px rgba(0,0,0,0.35);overflow:auto;padding:18px;">
      <button id="giftModalClose" style="position:absolute;top:12px;right:12px;background:#fff;border-radius:6px;border:none;padding:6px 10px;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.12);">✖</button>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(45deg,#8b6f47,#b99c6a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">🎁</div>
          <div>
            <h2 style="margin:0;color:#3b2b5a;">Gift wrapping options</h2>
            <div style="color:#6b6b7a;font-size:14px;">Choose a wrapper, card and accessories. Preview images and prices shown.</div>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
          <button id="giftSkipTop" style="background:transparent;border:1px solid #ddd;padding:8px 10px;border-radius:8px;cursor:pointer;color:#333;">No thanks — Pay</button>
        </div>
      </div>
      <div id="giftModalInner" style="display:flex;flex-direction:column;gap:12px;padding-bottom:16px;">
        <div id="giftModalContent" style="display:block;">
          <!-- dynamically injected -->
        </div>
      </div>
      <div style="position:sticky;bottom:0;left:0;right:0;background:linear-gradient(180deg, rgba(255,255,255,0), rgba(247,243,255,1));padding:12px 0;display:flex;justify-content:flex-end;gap:8px;backdrop-filter: blur(2px);">
        <button id="giftSkipBtn" style="background:#f3f4f6;border:none;padding:10px 16px;border-radius:8px;cursor:pointer;">No thanks</button>
        <button id="giftConfirmBtn" style="background:#6b46c1;color:#fff;border:none;padding:10px 16px;border-radius:8px;cursor:pointer;">Add to order</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('checkout-form');
      const makeBtn = document.querySelector('button[name="make_payment"]');
      const productsList = document.querySelector('.summary-products ul');
      const subtotalEl = document.getElementById('subtotal');
      let giftSelection = { wrapper: null, card: null, accessories: [], message: '' };
      let giftFetched = false;
      const giftModal = document.getElementById('giftModal');
      const giftContent = document.getElementById('giftModalContent');
      const giftClose = document.getElementById('giftModalClose');
      const giftSkip = document.getElementById('giftSkipBtn');
      const giftSkipTop = document.getElementById('giftSkipTop');
      const giftConfirm = document.getElementById('giftConfirmBtn');

      let giftOptions = null; // hold fetched options
      function renderGiftOptions(data) {
        giftOptions = data.options;
        giftContent.innerHTML = '';
        // ask wrapper
        if (data.options.wrapper.length) {
          const div = document.createElement('div');
          div.innerHTML = '<h3>Choose a wrapper</h3>';
          data.options.wrapper.forEach(w => {
            const id = 'wrap_'+w.id;
            const el = document.createElement('label');
            el.style.display='block';
            el.innerHTML = `<input type="radio" name="gift_wrapper" value="${w.id}"> ${w.name} (₹${w.price})`;
            if (w.img) {
              const img = document.createElement('img');
              img.src = '../'+w.img;
              img.style.maxHeight='40px';
              img.style.marginLeft='8px';
              el.appendChild(img);
            }
            if (w.description) {
              const desc = document.createElement('div');
              desc.style.fontSize='0.9em';
              desc.style.color='#555';
              desc.textContent = w.description;
              el.appendChild(desc);
            }
            div.appendChild(el);
          });
          giftContent.appendChild(div);
        }
        // card
        if (data.options.card.length) {
          const div = document.createElement('div');
          div.innerHTML = '<h3>Choose a card</h3>';
          data.options.card.forEach(c => {
            const el = document.createElement('label');
            el.style.display='block';
            el.innerHTML = `<input type="radio" name="gift_card" value="${c.id}"> ${c.name} (₹${c.price})`;
            if (c.img) {
              const img = document.createElement('img');
              img.src = '../'+c.img;
              img.style.maxHeight='40px';
              img.style.marginLeft='8px';
              el.appendChild(img);
            }
            if (c.description) {
              const desc = document.createElement('div');
              desc.style.fontSize='0.9em';
              desc.style.color='#555';
              desc.textContent = c.description;
              el.appendChild(desc);
            }
            div.appendChild(el);
          });
          const msgEl = document.createElement('div');
          msgEl.innerHTML = '<label>Message (optional):<br><textarea id="giftMessage" rows="2"></textarea></label>';
          div.appendChild(msgEl);
          giftContent.appendChild(div);
        }
        // accessories
        if (data.options.accessory.length) {
          const div = document.createElement('div');
          div.innerHTML = '<h3>Accessories (you may pick more than one)</h3>';
          data.options.accessory.forEach(a => {
            const el = document.createElement('label');
            el.style.display='block';
            el.innerHTML = `<input type="checkbox" name="gift_accessory" value="${a.id}"> ${a.name} (₹${a.price})`;
            if (a.img) {
              const img = document.createElement('img');
              img.src = '../'+a.img;
              img.style.maxHeight='40px';
              img.style.marginLeft='8px';
              el.appendChild(img);
            }
            if (a.description) {
              const desc = document.createElement('div');
              desc.style.fontSize='0.9em';
              desc.style.color='#555';
              desc.textContent = a.description;
              el.appendChild(desc);
            }
            div.appendChild(el);
          });
          giftContent.appendChild(div);
        }
      }

      function openGiftModal() {
        if (!giftFetched) {
          fetch('get_gift_options.php')
            .then(r => r.json())
            .then(data => {
              if (data.success) {
                const count = (data.options.wrapper?.length||0) + (data.options.card?.length||0) + (data.options.accessory?.length||0);
                if (count === 0) {
                  // nothing to choose, go ahead
                  submitOrder();
                  return;
                }
                renderGiftOptions(data);
                giftFetched = true;
                giftModal.style.display = 'block';
              } else {
                // backend error or empty
                submitOrder();
              }
            }).catch(() => {
              submitOrder();
            });
        } else {
          giftModal.style.display = 'block';
        }
      }

      function collectGiftSelection() {
        const wrapper = giftModal.querySelector('input[name="gift_wrapper"]:checked');
        giftSelection.wrapper = wrapper ? parseInt(wrapper.value) : null;
        const card = giftModal.querySelector('input[name="gift_card"]:checked');
        giftSelection.card = card ? parseInt(card.value) : null;
        giftSelection.accessories = Array.from(giftModal.querySelectorAll('input[name="gift_accessory"]:checked')).map(i => parseInt(i.value));
        const msg = giftModal.querySelector('#giftMessage');
        giftSelection.message = msg ? msg.value.trim() : '';
      }

      function closeGiftModal() {
        giftModal.style.display = 'none';
      }

      giftClose.addEventListener('click', closeGiftModal);
      giftSkip.addEventListener('click', function () {
        giftSelection = {wrapper:null,card:null,accessories:[],message:''};
        closeGiftModal();
        submitOrder();
      });
      if (giftSkipTop) {
        giftSkipTop.addEventListener('click', function () {
          giftSelection = {wrapper:null,card:null,accessories:[],message:''};
          closeGiftModal();
          submitOrder();
        });
      }
      giftConfirm.addEventListener('click', function () {
        collectGiftSelection();
        // update summary total with gift prices before submitting
        let extra = 0;
        if (giftOptions) {
          if (giftSelection.wrapper && giftOptions.wrapper) {
            const w = giftOptions.wrapper.find(o=>o.id==giftSelection.wrapper);
            if (w) extra += parseFloat(w.price);
          }
          if (giftSelection.card && giftOptions.card) {
            const c = giftOptions.card.find(o=>o.id==giftSelection.card);
            if (c) extra += parseFloat(c.price);
          }
          if (giftSelection.accessories && giftOptions.accessories) {
            giftSelection.accessories.forEach(id=>{
              const a = giftOptions.accessories.find(o=>o.id==id);
              if (a) extra += parseFloat(a.price);
            });
          }
        }
        if (extra > 0) {
          // update gift row and total display
          const giftRow = document.getElementById('gift-row');
          giftRow.style.display = '';
          giftRow.querySelector('span:last-child').textContent = '₹' + extra;
          const currentTotal = parseInt(document.querySelector('.summary-total span:last-child').textContent.replace(/[^0-9]/g, '')) || 0;
          document.querySelector('.summary-total span:last-child').textContent = '₹' + (currentTotal + extra);
        }
        closeGiftModal();
        submitOrder();
      });

      makeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        openGiftModal();
      });

      function submitOrder() {
        const formData = new FormData(form);
        const payload = {
          fullname: formData.get('fullname') || '',
          phone: formData.get('phone') || '',
          altphone: formData.get('altphone') || '',
          address: formData.get('address') || '',
          gift: giftSelection
        };
        makeBtn.disabled = true;
        const oldText = makeBtn.textContent;
        makeBtn.textContent = 'Processing...';

        fetch('place_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(r => r.text())
          .then(text => {
            try {
              const resp = JSON.parse(text);
              if (resp.success) {
                window.location.href = resp.redirect || '../orderPage/confirm.php';
              } else {
                alert(resp.message || 'Failed to place order');
                makeBtn.disabled = false;
                makeBtn.textContent = oldText;
              }
            } catch (e) {
              console.error('Order error — non-JSON response:', text);
              alert('Server error while placing order. See console for details.');
              makeBtn.disabled = false;
              makeBtn.textContent = oldText;
            }
          }).catch(err => {
            console.error('Order fetch failed', err);
            alert('An error occurred while placing the order.');
            makeBtn.disabled = false;
            makeBtn.textContent = oldText;
          });
      }

      // Fetch cart and user data to populate form/summary
      fetch('get_cart.php')
        .then(r => r.json())
        .then(data => {
          if (!data.success) return;
          // populate products
          productsList.innerHTML = '';
          data.cart.forEach(item => {
            const li = document.createElement('li');
            li.textContent = item.name + ' × ' + item.qty;
            const span = document.createElement('span');
            span.style.float = 'right';
            span.textContent = '₹' + (parseInt(item.price.replace(/[^0-9]/g, '')) * item.qty);
            li.appendChild(span);
            productsList.appendChild(li);
          });
          // update subtotal/total
          subtotalEl.querySelectorAll('span')[1].textContent = '₹' + data.subtotal;
          document.querySelector('.summary-total span:last-child').textContent = '₹' + data.total;
          // prefill name/phone if available
          if (data.user) {
            const nameInput = form.querySelector('input[name="fullname"]');
            const phoneInput = form.querySelector('input[name="phone"]');
            if (nameInput && data.user.name) nameInput.value = data.user.name;
            if (phoneInput && data.user.phone) phoneInput.value = data.user.phone;
          }
        }).catch(err => console.error('Failed to fetch cart:', err));

      // Handle form submit via AJAX
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const payload = {
          fullname: formData.get('fullname') || '',
          phone: formData.get('phone') || '',
          altphone: formData.get('altphone') || '',
          address: formData.get('address') || ''
        };

        makeBtn.disabled = true;
        const oldText = makeBtn.textContent;
        makeBtn.textContent = 'Processing...';

        fetch('place_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(r => r.text())
          .then(text => {
            try {
              const resp = JSON.parse(text);
              if (resp.success) {
                window.location.href = resp.redirect || '../orderPage/confirm.php';
              } else {
                alert(resp.message || 'Failed to place order');
                makeBtn.disabled = false;
                makeBtn.textContent = oldText;
              }
            } catch (e) {
              // Not valid JSON — likely HTML error output. Log and show raw body for debugging
              console.error('Order error — non-JSON response:', text);
              alert('Server error while placing order. See console for details.');
              makeBtn.disabled = false;
              makeBtn.textContent = oldText;
            }
          }).catch(err => {
            console.error('Order fetch failed', err);
            alert('An error occurred while placing the order.');
            makeBtn.disabled = false;
            makeBtn.textContent = oldText;
          });
      });
    });
  </script>
</body>

</html>
<?php require_once('../footerPage/footer.php'); ?>