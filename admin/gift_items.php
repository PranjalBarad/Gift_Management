<?php
require_once __DIR__ . '/auth.php';
include_once __DIR__ . '/../db.php';

// fetch existing gift items grouped by type
$items = ['wrapper' => [], 'card' => [], 'accessory' => []];
$res = $conn->query("SELECT * FROM gift_items ORDER BY type, name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $items[$row['type']][] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Wrapper - Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* minimal page-specific adjustments (main styles live in assets/style.css) */
        .gift-list { margin-top: 12px; }
        .gift-list table { width: 100%; border-collapse: collapse; }
        .gift-list th, .gift-list td { padding: 6px; }
        .delBtn { padding: 8px 12px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="gift_items.php" class="active">🎀 Gift Wrapper</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="contactus.php">📨 Contact Us</a>
    <a href="logout.php">🚪 Logout</a>
</div>
<div class="main">
    <div class="topbar">
        <h1>Gift wrapping / cards / accessories</h1>
    </div>

    <div class="content" style="padding:16px;">
        <p>You can add items of three types; these will be offered during checkout when the customer opts for gift wrapping.</p>

        <!-- add buttons to open forms -->
        <div style="display:flex;gap:12px;margin-bottom:12px;">
            <button id="openAddWrapper" class="btn">➕ Add Wrapper</button>
            <button id="openAddCard" class="btn">➕ Add Card</button>
            <button id="openAddAccessory" class="btn">➕ Add Accessory</button>
        </div>

        <!-- forms (hidden until button click) -->
        <div class="form-block" id="formWrapper" style="display:none;">
            <h3>Add wrapper</h3>
            <form id="formWrapperForm" method="post" action="add_gift_item.php" enctype="multipart/form-data">
                <input type="hidden" name="type" value="wrapper">
                <label>Name:<br><input type="text" name="name" required></label><br>
                <label>Price:<br><input type="number" name="price" step="0.01" required></label><br>
                <label>Description:<br><textarea name="description"></textarea></label><br>
                <label>Image:<br><input type="file" name="img" accept="image/*"></label><br>
                <button type="submit">Save wrapper</button>
                <button type="button" id="cancelWrapper">Cancel</button>
            </form>
        </div>

        <div class="form-block" id="formCard" style="display:none;">
            <h3>Add card</h3>
            <form id="formCardForm" method="post" action="add_gift_item.php" enctype="multipart/form-data">
                <input type="hidden" name="type" value="card">
                <label>Name:<br><input type="text" name="name" required></label><br>
                <label>Price:<br><input type="number" name="price" step="0.01" required></label><br>
                <label>Description:<br><textarea name="description"></textarea></label><br>
                <label>Image:<br><input type="file" name="img" accept="image/*"></label><br>
                <button type="submit">Save card</button>
                <button type="button" id="cancelCard">Cancel</button>
            </form>
        </div>

        <div class="form-block" id="formAccessory" style="display:none;">
            <h3>Add accessory</h3>
            <form id="formAccessoryForm" method="post" action="add_gift_item.php" enctype="multipart/form-data">
                <input type="hidden" name="type" value="accessory">
                <label>Name:<br><input type="text" name="name" required></label><br>
                <label>Price:<br><input type="number" name="price" step="0.01" required></label><br>
                <label>Description:<br><textarea name="description"></textarea></label><br>
                <label>Image:<br><input type="file" name="img" accept="image/*"></label><br>
                <button type="submit">Save accessory</button>
                <button type="button" id="cancelAccessory">Cancel</button>
            </form>
        </div>

        <!-- small success modal -->
        <div id="successModal" style="display:none;position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:2000;">
            <div style="background:#fff;max-width:420px;margin:20vh auto;padding:18px;border-radius:8px;text-align:center;">
                <div id="successMessage">Saved</div>
                <div style="margin-top:12px;"><button id="successOk">OK</button></div>
            </div>
        </div>

        <!-- listing -->
        <div class="gift-list">
            <h3>Existing items</h3>
            <?php foreach ($items as $type => $list): ?>
                <h4><?php echo ucfirst($type); ?>s</h4>
                <?php if (empty($list)): ?>
                    <p>No <?php echo $type; ?>s added yet.</p>
                <?php else: ?>
                    <table>
                        <tr><th>ID</th><th>Name</th><th>Price</th><th>Description</th><th>Image</th><th>Action</th></tr>
                        <?php foreach ($list as $it): ?>
                            <tr data-id="<?php echo $it['id']; ?>">
                                <td><?php echo $it['id']; ?></td>
                                <td><?php echo htmlspecialchars($it['name']); ?></td>
                                <td>₹<?php echo number_format($it['price'],2); ?></td>
                                <td><?php echo htmlspecialchars($it['description']); ?></td>
                                <td><?php if ($it['img']): ?><img src="../<?php echo htmlspecialchars($it['img']); ?>" style="max-height:40px;"><?php endif; ?></td>
                                <td><button class="delBtn" data-id="<?php echo $it['id']; ?>">Remove</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
    <script>
        (function(){
            const openWrapper = document.getElementById('openAddWrapper');
            const openCard = document.getElementById('openAddCard');
            const openAccessory = document.getElementById('openAddAccessory');
            const formWrapper = document.getElementById('formWrapper');
            const formCard = document.getElementById('formCard');
            const formAccessory = document.getElementById('formAccessory');
            const cancelWrapper = document.getElementById('cancelWrapper');
            const cancelCard = document.getElementById('cancelCard');
            const cancelAccessory = document.getElementById('cancelAccessory');
            const successModal = document.getElementById('successModal');
            const successMessage = document.getElementById('successMessage');
            const successOk = document.getElementById('successOk');

            openWrapper.addEventListener('click', ()=>{ formWrapper.style.display='block'; formCard.style.display='none'; formAccessory.style.display='none'; window.scrollTo(0, formWrapper.offsetTop); });
            openCard.addEventListener('click', ()=>{ formCard.style.display='block'; formWrapper.style.display='none'; formAccessory.style.display='none'; window.scrollTo(0, formCard.offsetTop); });
            openAccessory.addEventListener('click', ()=>{ formAccessory.style.display='block'; formWrapper.style.display='none'; formCard.style.display='none'; window.scrollTo(0, formAccessory.offsetTop); });
            cancelWrapper.addEventListener('click', ()=>{ formWrapper.style.display='none'; });
            cancelCard.addEventListener('click', ()=>{ formCard.style.display='none'; });
            cancelAccessory.addEventListener('click', ()=>{ formAccessory.style.display='none'; });

            // helper to submit form via AJAX
            function ajaxFormSubmit(form, onSuccess){
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    const fd = new FormData(form);
                    fetch(form.action, { method: 'POST', body: fd })
                      .then(r => r.json())
                      .then(json => {
                        if (json && json.success){
                            successMessage.textContent = 'Saved successfully.';
                            successModal.style.display = '';
                            onSuccess && onSuccess(json);
                        } else {
                            successMessage.textContent = (json && json.message) ? json.message : 'Failed to save';
                            successModal.style.display = '';
                        }
                      }).catch(err => {
                        successMessage.textContent = 'Server error';
                        successModal.style.display = '';
                      });
                });
            }

            ajaxFormSubmit(document.getElementById('formWrapperForm'), ()=>{ location.reload(); });
            ajaxFormSubmit(document.getElementById('formCardForm'), ()=>{ location.reload(); });
            ajaxFormSubmit(document.getElementById('formAccessoryForm'), ()=>{ location.reload(); });

            successOk.addEventListener('click', function(){ successModal.style.display='none'; });

            // delete buttons
            document.querySelectorAll('.delBtn').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');
                    if (!confirm('Remove this item?')) return;
                    fetch('delete_gift_item.php', { method: 'POST', headers: { 'Content-Type':'application/json' }, body: JSON.stringify({ id: id }) })
                      .then(r=>r.json())
                      .then(j=>{
                        if (j && j.success){
                            // remove row from DOM
                            const tr = document.querySelector('tr[data-id="'+id+'"]');
                            if (tr) tr.remove();
                        } else {
                            alert((j && j.message) ? j.message : 'Failed to delete');
                        }
                      }).catch(()=> alert('Server error'));
                });
            });
        })();
    </script>
</body>
</html>
