<?php
require_once('../headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact us - WowBox</title>
 <link rel="stylesheet" href="../../project/contactusPage/contactus.css">
     <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
</head>
<body>
    <div class="contact_div">

  <!-- Contact Us Section -->
  <section class="contact-header">
    <div class="contact-left">
      <h1>Contact Us</h1>
      <p>We'd love to hear from you! Wheather you have question, feedback, or just want to connect, our team is always here to help.</p>
    </div>
    <div class="contact-right">
      <!-- phone.png -->
      <img src="../../project/photos/co1_9.jpeg" alt="Contact Image">
    </div>
  </section>

  <!-- Contact Information -->
  <section class="contact-info">
    <h2>Let's connect !</h2>
    <p>Have a question or idea..? Reach out to us. contact us anytime, Your questions matter and we'll be happy to help. </p>
    <div class="info-box">
      <div>
        <i class="fa-solid fa-phone fa-bounce"></i>
        <p>(+91) 9624051234</p>
      </div>
      <div>
        <i class="fa fa-envelope"></i>
        <p>wowbox@gmail.com</p>
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
    <form>
      <input type="text" placeholder="Name" required>
      <input type="email" placeholder="Email" required>
      <textarea placeholder="Message" required></textarea>
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