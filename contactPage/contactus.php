<?php
require_once('../headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact us - WowBox</title>
    <link rel="stylesheet" href="contactus.css">
     <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
</head>
<body style="margin-top: 400px;">
    <div class="contact_div">

  <!-- Contact Us Section -->
  <section class="contact-header">
    <div class="contact-left">
      <h1>Contact Us</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
    </div>
    <div class="contact-right">
      <img src="kitchen.jpg" alt="Contact Image">
    </div>
  </section>

  <!-- Contact Information -->
  <section class="contact-info">
    <h2>Contact Information</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.</p>
    <div class="info-box">
      <div>
        <i class="fa fa-phone"></i>
        <p>(+654) 6544 55</p>
      </div>
      <div>
        <i class="fa fa-envelope"></i>
        <p>mail@ktchn.com</p>
      </div>
      <div>
        <i class="fa fa-map-marker"></i>
        <p>London Eye, UK</p>
      </div>
    </div>
  </section>

  <!-- Get In Touch Form -->
  <section class="contact-form">
    <h2>Get In Touch !</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.</p>
    <form>
      <input type="email" placeholder="Email" required>
      <input type="text" placeholder="Name" required>
      <textarea placeholder="Message" required></textarea>
      <button type="submit">Submit</button>
    </form>
  </section>

    </div>
</body>
</html>
<?php
require_once('../footerPage/footer.php');
?>