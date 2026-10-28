<?php
require_once('../../project/headerPage/header.php');
// load DB connection
require_once __DIR__ . '/../db.php';

// Fetch categories: prefer categories table if exists, else derive from products
$categories = [];
$catRes = $conn->query("SHOW TABLES LIKE 'categories'");
if ($catRes && $catRes->num_rows > 0) {
  $r = $conn->query('SELECT id, name, img FROM categories ORDER BY name');
  if ($r) {
    while ($c = $r->fetch_assoc()) {
      $categories[] = $c;
    }
    $r->free();
  }
} else {
  // derive categories from products table
  $p = $conn->query("SELECT DISTINCT TRIM(category) AS category FROM products WHERE IFNULL(category,'')!='' ORDER BY category");
  if ($p) {
    while ($row = $p->fetch_assoc()) {
      $categories[] = ['id' => 0, 'name' => $row['category'], 'img' => ''];
    }
    $p->free();
  }
}

// Fetch products for display: show only premium products (limit 5)
$products = [];
$prodRes = $conn->query("SELECT id, name, category, description, includes, price, img, IFNULL(is_premium,0) as is_premium FROM products WHERE IFNULL(is_premium,0)=1 ORDER BY id DESC LIMIT 5");
if ($prodRes) {
  while ($pr = $prodRes->fetch_assoc()) {
    $products[] = $pr;
  }
  $prodRes->free();
}

// Helper: resolve image path stored in DB to a usable src relative to this file.
function resolveImagePath($img, $fallback = null)
{
  $img = trim((string) $img);
  if ($img === '') {
    return $fallback ?: '../photos/men.png';
  }
  // absolute URL or root-relative path
  if (preg_match('#^https?://#i', $img) || strpos($img, '/') === 0) {
    return $img;
  }
  // check common locations on disk relative to this file
  $candidates = [
    __DIR__ . '/../' . $img,
    __DIR__ . '/../photos/' . $img,
    __DIR__ . '/' . $img,
  ];
  foreach ($candidates as $idx => $candidate) {
    if (file_exists($candidate)) {
      // Map candidate back to a web-relative path from this PHP file
      if ($idx === 0)
        return '../' . $img;
      if ($idx === 1)
        return '../photos/' . $img;
      return $img; // same directory
    }
  }
  // Last resort: assume image lives in ../photos/
  return '../photos/' . ltrim($img, '/');
}

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
    <img class="img_home" src="../../project/photos/BG.jpg" alt="">
    <div class="hero-text">
      <div class="hero-head">Wrap Up Some Love!</div>
      <div class="hero-para">Fill it with their favorites, add a heartfelt note because when it comes from the heart,
        every gift becomes unforgettable.</div>

      <a href="../../project/shopPage/shop2.php"><button class="hero-btn" type="submit">SHOP NOW</button></a>
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
                    <a href="../../project/shopPage/shop2.php?category=forhim"><img src="../../project/photos/him.jpg" class="card-img" alt=""></a>
                <div class="card-text">FOR HIM</div>
                </div>

                <div class="card card-2">
                    <a href="../../project/shopPage/shop2.php?category=forkids"><img src="../../project/photos/kids.jpg" class="card-img" alt=""></a>
                <div class="card-text">FOR KIDS</div>
                </div>

                <div class="card card-3">
                    <a href="../../project/shopPage/shop2.php?category=forher"><img src="../../project/photos/her.jpg" class="card-img" alt=""></a>
                <div class="card-text">FOR HER</div>
                </div>

                <div class="card card-4">
                <a href="../../project/shopPage/shop2.php?category=birthday"><img src="../../project/photos/birthday.jpg" class="card-img" alt=""></a>
                <div class="card-text">BIRTHDAY</div>
                </div>

                <div class="card card-5">
                <a href="../../project/shopPage/shop2.php?category=wedding"><img src="../../project/photos/wedding.jpg" class="card-img" alt=""></a>
                <div class="card-text">WEDDING</div>
                </div>

                <div class="card card-6">
                <a href="../../project/shopPage/shop2.php?category=anniversary"><img src="../../project/photos/Anniversary.jpg" class="card-img" alt=""></a>
                <div class="card-text">ANNIVERSARY</div>
                </div>
            </div>
        </section>




    <!--         CREATE OWN BOX  !!           -->

    <div class="create">
      <div class="boximage">
        <img src="../../project/photos/h.png" alt="" class="createImage">
      </div>
      <div class="createBox">
        <h2>Perfect Wedding Gifts</h2>
        <h4>Make their big day unforgettable with timeless wedding treasures.</h4>
        <button class="createBtn" onclick="location.href='../../project/shopPage/shop2.php?category=wedding'">Shop
          Now</button>
      </div>
      <img src="../photos/wedd.png" alt="" class=" boximage createImage2">
    </div>


    <!--            BEST SELLER !!          -->

    <section class="bestSeller">
      <div class="bestSeller-head">
        <div>
          <div class="bestSeller-head-text">Premium Series</div>
          <div class="bestSeller-head-para">Thoughtfully curated hampers to show some love to you special ones.</div>
        </div>
        <button class="bestSeller-cta" onclick="location.href='../../project/shopPage/shop2.php?category=premium'">Catch Up Now</button>
      </div>

      <div class="bestSeller-cards">
        <?php if (empty($products)): ?>
          <div>No products available.</div>
        <?php else: ?>
          <?php foreach ($products as $j => $pr): ?>
            <?php
            $pName = htmlspecialchars($pr['name']);
            // use the same slug format for product category param so shop2.php mapping matches
            $pCat = preg_replace('/[^a-z0-9_\-]/', '', strtolower($pr['category']));
            $pImg = resolveImagePath($pr['img'] ?? '', '../../photos/men.png');
            $label = $pName;
            ?>
            <div class="Bcard Bcard-<?= ($j + 1) ?>">
              <a href="../../project/shopPage/shop2.php?category=premium&product=<?= (int) $pr['id'] ?>">
                <img src="<?= htmlspecialchars($pImg) ?>" class="card-img" alt="<?= $pName ?>">
                <div class="card-text"><?= $label ?></div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>


    <!--       REVIEWS           -->

    <div class="reviews">
      <h2 class="clr">WHAT OUR CUSTOMERS SAY ?</h2>

      <div class="Review_all">
        <div class="stars">★★★★</div>
        <p class="review-text">
          Honestly, I was a little late in ordering a birthday gift for my best friend But it arrived right on time,
          beautifully packed, and my friend absolutely loved the curation. Safe to say I'm the favourite friend now!
        </p>
        <div class="clr name"> Krishna</div>
      </div>

      <div class="Review_all">
        <div class="stars">★★★★★</div>
        <p class="review-text">
          After 8 years together, finding a gift that feels new isn't easy. But this really stood out, I got to pick
          everything and make a gift box myself based on what she loves.. She absolutely loved it.
        </p>
        <div class="clr name"> Shreya</div>
      </div>

      <div class="Review_all">
        <!-- <div class="Review3"> -->
        <div class="stars">★★★★</div>
        <p class="review-text">
          This was my first time ordering from Buze Co, and I wasn't sure how it would turn out. But the whole
          experience was smooth, and the gift looked premium. Will definitely reorder.
        </p>
        <div class="clr name"> Rohan</div>
      </div>
    </div>
    <!-- Welcome to Buze Co -->
    <div class="readMore">
      <input type="checkbox" id="ch">
      <p class="read-head">Welcome to Buze Co - Where Gifting Becomes a Thoughtful Experience</p>
      <p>
        Gifting is more than a gesture — it's a way to express emotions, create memories, and strengthen bonds. At
        Buze Co, we bring you a carefully curated range of luxury gift hampers designed to elevate every occasion.
        Whether you're celebrating a birthday, anniversary, festival, or simply showing appreciation, our handcrafted
        hampers are made to leave a lasting impression.
      </p>

      <div class="more-text">
        <p class="read-head">Why Choose Buze Co?</p>
        <p>✔ Wide Range of Curated Hampers - From gourmet treats to wellness kits, we have something for every taste and
          occasion</p>
        <p>✔ Customizable Options - Personalize your hampers to reflect your thoughts and style.</p>
        <p>✔ Premium Packaging - Elegantly designed boxes for a truly luxurious unboxing experience.</p>
        <p>✔ Pan-India Delivery - Deliver happiness anywhere, right on time. </p>
        <p>✔ Seamless Ordering - Simple, secure, and user-friendly online experience.</p>
        <p>At Gift Heaven, we don't just deliver gifts — we deliver emotions wrapped in elegance. Explore our collection
          or create your own personalized hamper to turn ordinary moments into unforgettable memories.</p>
        <label for="ch"> Show Less...</label>
      </div>
      <label for="ch"> Read More...</label> <!-- when ch checked this label will hidden -->
    </div>
  </main>
</body>

</html>

<?php
require_once('../Footer/footer.php');
?>


