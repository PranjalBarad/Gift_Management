<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - WowBox</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
    }

    section {
      padding: 60px 20px;
      max-width: 1100px;
      margin: auto;
    }

    h2 {
      font-size: 32px;
      margin-bottom: 20px;
    }

    .row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 60px;
    }

    .row .text {
      flex: 1;
      min-width: 280px;
      padding: 20px;
    }

    .row .image {
      flex: 1;
      min-width: 280px;
      padding: 20px;
    }

    .row img {
      width: 100%;
      max-width:40%;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .overlap {
      position: relative;
    }

    .overlap .text-box {
      position: absolute;
      bottom: -30px;
      left: 30px;
      background: white;
      padding: 20px;
      max-width: 400px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    .features {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin: 40px 0;
    }

    .feature {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      width: 200px;
      text-align: center;
    }

    .cta {
      background: #fff8f0;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
      margin: auto;
      max-width: 600px;
    }

    .cta button {
      margin-top: 15px;
      padding: 10px 20px;
      border: 2px solid #333;
      background: transparent;
      border-radius: 5px;
      cursor: pointer;
    }

    footer {
      text-align: center;
      padding: 40px 20px;
    }

    footer .icons {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 20px;
    }

    footer .icon {
      width: 150px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .row {
        flex-direction: column;
      }
      .overlap .text-box {
        position: static;
        max-width: 100%;
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- About Us -->
  <section>
    
    <div class="row">
      <div class="text">
        <h2>About Us</h2>
        <p>
          Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. 
          Vestibulum ante ipsum primis in faucibus orci luctus et ultrices 
          posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, 
          ullamcorper sit amet ligula.
        </p>
      </div>
      <div class="image">
        <img src="../../project/photos/men.png" alt="About Us Image">
      </div>
    </div>
  </section>

  <!-- Who We Are -->
  <section class="overlap">
    <div class="row">
      <!-- <div class="image"> -->
        <img class="image" src="../../project/photos/love.png" alt="Who We Are">
      <!-- </div> -->
      <div class="text-box">
        <h2>Who We Are</h2>
        <p>
          Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. 
          Vestibulum ante ipsum primis in faucibus orci luctus et ultrices 
          posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, 
          ullamcorper sit amet ligula.
        </p>
      </div>
    </div>
  </section>

  <!-- What We Do -->
  <section>
    <h2 style="text-align:center;">What We Do</h2>
    <div class="features">
      <div class="feature">
        <h3>Icon 1</h3>
        <p>Lorem Ipsum text here</p>
      </div>
      <div class="feature">
        <h3>Icon 2</h3>
        <p>Lorem Ipsum text here</p>
      </div>
      <div class="feature">
        <h3>Icon 3</h3>
        <p>Lorem Ipsum text here</p>
      </div>
    </div>
  </section>

  <!-- Plan a Visit -->
  <section>
    <div class="cta">
      <h2>Plan a Visit</h2>
      <p>
        Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. 
        Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.
      </p>
      <button>Book an Appointment</button>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="icons">
      <div class="icon">
        <h3>Come On In</h3>
        <p>Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui.</p>
      </div>
      <div class="icon">
        <h3>Get in Touch</h3>
        <p>Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui.</p>
      </div>
    </div>
  </footer>

</body>
</html>