<?php
require_once('../headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../project/homePage/home.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
</head>
<body>
    <section class="img-slider">
            <img class="img_home" src="../../project/photos/1.png" alt="">
            <div class="hero-text"> 
                <div class="hero-head">Wrap Up Some Love!</div>
                <div class="hero-para">Fill it with their favorites, add a heartfelt note  because when it comes from the heart, every gift becomes unforgettable.</div>

            <button class="hero-btn" type="submit">SHOP NOW</button>
            </div>
        </section>

<!--            GIFTS BY CATEGORIES !!           -->
    <main class="container">

        <section class="categories">
            <div class="categories-head">
                <div class="categories-head-text">GIFTS BY CATEGORIES</div>
                <div class="categories-head-para">Best gift hampers for your loved ones.</div>
            </div>
     
            <div class="categories-cards">
                <div class="card card-1">
                    <img src="../../project/photos/for her.png" class="card-img" alt="">
                <div class="card-text">FOR HER</div>
                </div>

                <div class="card card-2">
                    <img src="../../project/photos/FOR HIM 1.png" class="card-img" alt="">
                <div class="card-text">FOR HIM</div>
                </div>

                <div class="card card-3">
                <img src="../../project/photos/for kids.png" class="card-img" alt="">
                <div class="card-text">FOR KIDS</div>
                </div>

                <div class="card card-4">
                <img src="../../project/photos/birthday.png" class="card-img" alt="">
                <div class="card-text">BIRTHDAY</div>
                </div>

                <div class="card card-5">
                <img src="../../project/photos/gournment.png" class="card-img" alt="">
                <div class="card-text">WEDDING</div>
                </div>

                <div class="card card-6">
                <img src="../../project/photos/aniversery.png" class="card-img" alt="">
                <div class="card-text">ANNIVERSARY</div>
                </div>
            </div>
        </section>


    <!--         CREATE OWN BOX  !!           -->

    <div class="create"> 
        <div class="boximage">    
        <img src="../photos/creategift.png" alt="" class="createImage" >
        </div>
        <div class="createBox">
            <h2>Perfect Wedding Gifts</h2>
            <h4>Make their big day unforgettable with timeless wedding treasures.</h4>
            <button class="createBtn">Shop Now</button>
        </div>
        <img src="../photos/wedd.png" alt="" class=" boximage createImage2" >
    </div>


    <!--            BEST SELLER !!          -->

    <section class="bestSeller">
            <div class="bestSeller-head">
                <div class="bestSeller-head-text">BEST SELLER</div>
                <div class="bestSeller-head-para">Thoughtfully curated hampers to show some love to you special ones.</div>
            </div>
     
            <div class="bestSeller-cards">
                <div class="Bcard Bcard-1">
                    <img src="../../project/photos/men.png" class="card-img" alt="">
                    <div class="card-text">MEN IN BLACK</div>
                </div>

                <div class="Bcard Bcard-2">
                    <img src="../../project/photos/bday2.jpg" class="card-img" alt="">
                    <div class="card-text">BIRTHDAY</div>
                </div>

                <div class="Bcard Bcard-3">
                    <img src="../../project/photos/love.png" class="card-img" alt="">
                    <div class="card-text">LOVE</div>
                </div>
            </div>
        </section>
        

        <!--       REVIEWS           -->

         <div class="reviews">
            <h2 class="clr">WHAT OUR CUSTOMERS SAY ?</h2>
            
                    <div class="Review_all">
                        <div class="stars">★★★★</div>
                        <p class="review-text">
                            Honestly, I was a little late in ordering a birthday gift for my best friend But it arrived right on time, beautifully packed, and my friend absolutely loved the curation. Safe to say I'm the favourite friend now!
                        </p>
                        <div class="clr name"> Krishna</div>
                    </div>

                    <div class="Review_all">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">
                            After 8 years together, finding a gift that feels new isn't easy. But this really stood out, I got to pick everything and make a gift box myself based on what she loves.. She absolutely loved it.
                        </p>
                        <div class="clr name"> Shreya</div>
                    </div>

                    <div class="Review_all">
                        <!-- <div class="Review3"> -->
                        <div class="stars">★★★★</div>
                        <p class="review-text">
                            This was my first time ordering from The WowBox, and I wasn't sure how it would turn out. But the whole experience was smooth, and the gift looked premium. Will definitely reorder.
                        </p>
                        <div class="clr name"> Rohan</div>
                    </div>
        </div>
        <!-- Welcome to WoWbox -->
    <div class="readMore">
        <input type="checkbox" id="ch">
        <p class="read-head">Welcome to WowBox - Where Gifting Becomes a Thoughtful Experience</p>
        <p>
            Gifting is more than a gesture — it's a way to express emotions, create memories, and strengthen bonds. At WowBox, we bring you a carefully curated range of luxury gift hampers designed to elevate every occasion. Whether you're celebrating a birthday, anniversary, festival, or simply showing appreciation, our handcrafted hampers are made to leave a lasting impression.
        </p>
        
        <div class="more-text">
            <p class="read-head">Why Choose WowBox?</p>
                <p>✔ Wide Range of Curated Hampers - From gourmet treats to wellness kits, we have something for every taste and occasion</p> 
                <p>✔ Customizable Options - Personalize your hampers to reflect your thoughts and style.</p>
                <p>✔ Premium Packaging - Elegantly designed boxes for a truly luxurious unboxing experience.</p> 
                <p>✔ Pan-India Delivery - Deliver happiness anywhere, right on time. </p>
                <p>✔ Seamless Ordering - Simple, secure, and user-friendly online experience.</p>
                <p>At Gift Heaven, we don't just deliver gifts — we deliver emotions wrapped in elegance. Explore our collection or create your own personalized hamper to turn ordinary moments into unforgettable memories.</p>
            <label for="ch"> Show Less...</label>
        </div>
        <label for="ch"> Read More...</label>   <!-- when ch checked this label will hidden -->
    </div>
    </main>
</body>
</html>

<?php
require_once('../footerPage/footer.php');
?>


<!-- <div class="card card-4">
                <img src="../../project/photos/birthday.png" class="card-img" alt="">
                <div class="card-text">BIRTHDAY</div>
                </div>

                <div class="card card-5">
                <img src="../../project/photos/gournment.png" class="card-img" alt="">
                <div class="card-text">WEDDING</div>
                </div>

                <div class="card card-6">
                <img src="../../project/photos/aniversery.png" class="card-img" alt="">
                <div class="card-text">ANNIVERSARY</div> -->