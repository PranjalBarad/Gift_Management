<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

// Accept form data (multipart/form-data expected)
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$includes = isset($_POST['includes']) ? trim($_POST['includes']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';
$hasIsPremium = array_key_exists('is_premium', $_POST);
$is_premium = $hasIsPremium ? (int) $_POST['is_premium'] : 0;

if ($name === '' || $price === '') {
  echo json_encode(['success' => false, 'message' => 'Name and price are required']);
  exit;
}

// Handle optional image upload
$imgPath = null;
if (!empty($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = __DIR__ . '/../photos';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }
  $tmp = $_FILES['img']['tmp_name'];
  $orig = basename($_FILES['img']['name']);
  $ext = pathinfo($orig, PATHINFO_EXTENSION);
  $safe = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($orig, PATHINFO_FILENAME));
  $newName = $safe . '_' . time() . '.' . $ext;
  $dest = $uploadDir . '/' . $newName;
  if (move_uploaded_file($tmp, $dest)) {
    // store relative path for web
    $imgPath = 'photos/' . $newName;
  }
}

// If category comes from select but categories table exists, optionally ensure category exists
// For simplicity, we'll store category as text in products.category as existing schema uses

// detect if products table has is_premium column
$hasPremium = false;
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'is_premium'");
if ($colRes && $colRes->num_rows > 0) {
  $hasPremium = true;
}

if ($id > 0) {
  // update
  // ensure price is a number
  $price = (float) $price;
  if ($imgPath) {
    if ($hasPremium) {
      $stmt = $conn->prepare('UPDATE products SET name=?, category=?, description=?, includes=?, price=?, img=?, is_premium=? WHERE id=?');
      if (!$stmt) {
        echo json_encode(['success' => false, 'message' => $conn->error]);
        exit;
      }
      $stmt->bind_param('ssssdsii', $name, $category, $description, $includes, $price, $imgPath, $is_premium, $id);
    } else {
      $stmt = $conn->prepare('UPDATE products SET name=?, category=?, description=?, includes=?, price=?, img=? WHERE id=?');
      if (!$stmt) {
        echo json_encode(['success' => false, 'message' => $conn->error]);
        exit;
      }
      $stmt->bind_param('ssssdsi', $name, $category, $description, $includes, $price, $imgPath, $id);
    }
  } else {
    if ($hasPremium && $hasIsPremium) {
      $stmt = $conn->prepare('UPDATE products SET name=?, category=?, description=?, includes=?, price=?, is_premium=? WHERE id=?');
      if (!$stmt) {
        echo json_encode(['success' => false, 'message' => $conn->error]);
        exit;
      }
      $stmt->bind_param('ssssdii', $name, $category, $description, $includes, $price, $is_premium, $id);
    } else {
      // do not touch is_premium when not provided
      $stmt = $conn->prepare('UPDATE products SET name=?, category=?, description=?, includes=?, price=? WHERE id=?');
      if (!$stmt) {
        echo json_encode(['success' => false, 'message' => $conn->error]);
        exit;
      }
      $stmt->bind_param('ssssdi', $name, $category, $description, $includes, $price, $id);
    }
  }
  if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
    exit;
  }
  echo json_encode(['success' => true, 'message' => 'Product updated']);
  exit;
} else {
  // insert
  if ($imgPath) {
    if ($hasPremium) {
      $stmt = $conn->prepare('INSERT INTO products (name, category, description, includes, price, img, is_premium) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('ssssdsi', $name, $category, $description, $includes, $price, $imgPath, $is_premium);
    } else {
      $stmt = $conn->prepare('INSERT INTO products (name, category, description, includes, price, img) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('ssssds', $name, $category, $description, $includes, $price, $imgPath);
    }
  } else {
    if ($hasPremium && $hasIsPremium) {
      $stmt = $conn->prepare('INSERT INTO products (name, category, description, includes, price, is_premium) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('ssssd i', $name, $category, $description, $includes, $price, $is_premium);
    } else if ($hasPremium) {
      // hasPremium but no is_premium provided: insert with default 0
      $stmt = $conn->prepare('INSERT INTO products (name, category, description, includes, price, is_premium) VALUES (?, ?, ?, ?, ?, 0)');
      $stmt->bind_param('ssssd', $name, $category, $description, $includes, $price);
    } else {
      $stmt = $conn->prepare('INSERT INTO products (name, category, description, includes, price) VALUES (?, ?, ?, ?, ?)');
      $stmt->bind_param('ssssd', $name, $category, $description, $includes, $price);
    }
  }
  if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
  }
  if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
    exit;
  }
  echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Product created']);
  exit;
}

?>