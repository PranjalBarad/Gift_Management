<?php
// Handle form submission before any output to allow redirect (Post-Redirect-Get)
$form_errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once(__DIR__ . '/../db.php');

  // Simple server-side validation and sanitization
  $name = isset($_POST['name']) ? trim($_POST['name']) : '';
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $message = isset($_POST['message']) ? trim($_POST['message']) : '';

  if ($name === '') {
    $form_errors[] = 'Name is required.';
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $form_errors[] = 'A valid email is required.';
  }
  if ($message === '') {
    $form_errors[] = 'Message is required.';
  }

  if (empty($form_errors)) {
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    if ($stmt) {
      $stmt->bind_param('sss', $name, $email, $message);
      if ($stmt->execute()) {
        // Redirect to avoid duplicate submission on refresh
        header('Location: ' . $_SERVER['PHP_SELF'] . '?sent=1');
        exit;
      } else {
        $form_errors[] = 'Error saving your message. Please try again later.';
      }
      $stmt->close();
    } else {
      $form_errors[] = 'Database error. Please try again later.';
    }
  }
}

require_once('../headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact us - Buze Co</title>
  <link rel="stylesheet" href="../../project/contactusPage/contactus.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
</head>

<body>
  <div class="contact_div">

    <!-- Contact Us Section -->
    <section class="contact-header">
      <div class="contact-left">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Wheather you have question, feedback, or just want to connect, our team is always
          here to help.</p>
      </div>
      <div class="contact-right">
        <!-- phone.png -->
        <img src="../../project/photos/co1_9.jpeg" alt="Contact Image">
      </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info">
      <h2>Let's connect !</h2>
      <p>Have a question or idea..? Reach out to us. contact us anytime, Your questions matter and we'll be happy to
        help. </p>
      <div class="info-box">
        <div>
          <i class="fa-solid fa-phone fa-bounce"></i>
          <p>(+91) 9624051234</p>
        </div>
        <div>
          <i class="fa fa-envelope"></i>
          <p>buze.co@gmail.com</p>
        </div>
        <div>
          <i class="fa-solid fa-location-dot"></i>
          <p>London Eye, UK</p>
        </div>
      </div>
    </section>

    <!-- Get In Touch Form -->
    <section class="contact-form">
      <div class="contact-form-img">
        <h2>Get In Touch !</h2>
        <p>Fill out the form and our team will respond to you as quickly as possible.</p>
        <?php
        // Show success message when redirected after POST
        if (isset($_GET['sent']) && $_GET['sent'] == '1') {
          echo '<div class="contact-success">Thanks! Your message has been received.</div>';
        }

        // Show validation or DB errors if any
        if (!empty($form_errors)) {
          echo '<div class="contact-error">' . implode('<br>', array_map('htmlspecialchars', $form_errors)) . '</div>';
        }
        ?>

        <form method="post" action="">
          <input type="text" name="name" placeholder="Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <textarea name="message" placeholder="Message" required></textarea>
          <button type="submit">Submit</button>
        </form>
      </div>
    </section>

  </div>
</body>

</html>
<?php
require_once('../footerPage/footer.php');
?>