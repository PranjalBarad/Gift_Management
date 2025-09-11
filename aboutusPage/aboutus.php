
<?php
require_once('../headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - WowBox</title>
  <link rel="stylesheet" href="aboutus.css">
</head>
<body>

  <!-- About Us Section -->
   <div class="aboutus_div">
  <section class="about">
    <div class="about-img">
      <img src="../../project/photos/book1.png" alt="WowBox Gift">
    </div>
    <div class="about-text">
      <h2>About Us</h2>
      <p>
        At <b>WowBox</b>, We make gifting simple and special. Every box is filled with love, care, and joy. From birthdays to celebrations, we create lasting memories. Because gifts are not just things — they are feelings.<br><br>We believe every gift is more than just a product. It's an emotion,
        a memory, and a smile waiting to happen. From birthdays to weddings, festivals to
        corporate events, WowBox is here to make every moment extraordinary with thoughtfully
        curated surprises.
      </p>
    </div>
    
  </section>

  <!-- Who We Are -->
  <section class="who">   
    <div class="who-text">
      <h2>Who We Are</h2>
      <p>At WowBox, we are dreamers, creators, and storytellers of joy. Every gift we design carries a touch of love, care, and magic — because we believe gifts are not just things, they are feelings wrapped beautifully. Our purpose is simple: to turn ordinary moments into unforgettable wow moments. 💝

      </p>
    </div>
    <div class="who-img">
      <!-- girl.png -->
    <img src="../../project/photos/co1_5.jpeg" alt="Who We Are">
    </div>  
</section>

  <!-- What We Do -->
  <section class="what">
    <h2>What We Do</h2>
    <div class="what-container">
      <div class="what-box">
        🎁
        <h3>Unique Collections</h3>
        <p>We curate trendy & thoughtful gift boxes to match every mood, occasion.</p>
      </div>
      <div class="what-box">
        📦
        <h3>Elegant Packaging</h3>
        <p>Each Box is wrapped with love and care to create the perfect surprise.</p>
      </div>
      <div class="what-box">
        🚚
        <h3>On-Time Delivery</h3>
        <p>Your happiness delivered right when you need it, without hassle.</p>
      </div>
    </div>
  </section>

  <!-- Plan a Visit -->
  <section class="visit">
    <div class="visit-box">
      <h2>Get in touch!</h2>
      <p>
        Have a question or special request? We're here to help you with inquiries, custom orders, and <br> support. Let's connect and make your experience memorable.
      </p>
      <a href="../../project/contactusPage/contactus.php">
      <button id="btn_id" onclick="contact_btn()">Contact now </button>
      </a>
    </div>

    <!-- <div class="visit-extra">
      <div class="extra-box">🏬 <h3>Come In</h3> <p>Step into WowBox and find your perfect gift.</p></div>
      <div class="extra-box">📞 <h3>Get in Touch</h3> <p>Contact us anytime — we’re here to help you.</p></div>
    </div> -->
  </section>
</div>
</body>
</html>

<?php
require_once('../footerPage/footer.php');
?>