<?php
require_once __DIR__ . '/../db.php';

$errors = [];
$success = '';
$name = '';
$email = '';
$message_text = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message_text = trim($_POST['message'] ?? '');

  if ($name === '') {
    $errors[] = 'Name is required.';
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
  }
  if ($message_text === '') {
    $errors[] = 'Message is required.';
  }

  if (empty($errors)) {
    try {
      $stmt = $conn->prepare('INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())');
      $stmt->bind_param('sss', $name, $email, $message_text);
      $stmt->execute();
      $success = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
      $name = $email = $message_text = '';
    } catch (Exception $e) {
      $errors[] = 'Failed to save message. Please try again.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Buze Co Gift Shop</title>
  <link rel="stylesheet" href="../common.css">
  <link rel="stylesheet" href="contact_us.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="Header">
    <?php require_once('../headerPage/header.php'); ?>
  </div>

  <section class="contact-darkW">
    <div class="contact-heroW">
      <h1>Get In Touch</h1>
      <p class="contact-descW">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
    </div>
    <div class="contact-mainW">
      <div class="contact-infoW">
        <h2 class="contact-titleW">Contact Information</h2>
        <div class="info-items">
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <h3>Location</h3>
              <p>500 Terry Francine Street<br>San Francisco, CA 94158</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
              <h3>Phone</h3>
              <p>+91 9586578682</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <h3>Email</h3>
              <p>buzeco.gift.official@email.com</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
              <h3>Hours</h3>
              <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</p>
            </div>
          </div>
        </div>
      </div>
      <div class="contact-formW">
        <h2 class="contact-titleW">Send a Message</h2>
        <?php if (!empty($success)): ?>
          <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <p><?php echo htmlentities($success); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="error-message">
            <ul>
              <?php foreach ($errors as $err): ?>
                <li><i class="fas fa-exclamation-circle"></i> <?php echo htmlentities($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" class="contact-form">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input 
              type="text" 
              id="name"
              name="name" 
              placeholder="Enter your full name" 
              required 
              value="<?php echo htmlentities($name); ?>"
            >
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input 
              type="email" 
              id="email"
              name="email" 
              placeholder="Enter your email" 
              required 
              value="<?php echo htmlentities($email); ?>"
            >
          </div>

          <div class="form-group">
            <label for="message">Message *</label>
            <textarea 
              id="message"
              name="message" 
              rows="5" 
              placeholder="Tell us how we can help you..."
              required
            ><?php echo htmlentities($message_text); ?></textarea>
          </div>

          <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane"></i> Send Message
          </button>
        </form>
      </div>
    </div>
  </section>

  <div class="Footer">
    <?php require_once('../footerPage/footer.php'); ?>
  </div>
</body>

</html>