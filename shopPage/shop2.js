function sortProducts(category) {
  let products = document.querySelectorAll('.product');
  let count = 0;

  products.forEach(product => {
    if (category === 'all' || product.classList.contains(category)) {
      product.style.display = "";   // show
      count++;
    } else {
      product.style.display = "none"; // hide
    }
  });

  document.querySelector(".shop-header p").textContent = `Showing ${count} results`;
}

sortProducts('all');

//model
const products = [
  {
    name: "Classic Luxury Box",
    price: "₹1999",
    img: "../../project/photos/FORHIM1.png",
    desc: "A bold mix of luxury and charm - the Luxe Elegance Box is the perfect hamper for anyone who loves premium surprises.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>1 x Premium Perfume Bottle</li>
        <li>1 x Luxury Dark Chocolate Bar</li>
        <li>1 x Stylish Wallet / Accessory</li>
      </ul>
    `
  },
  {
    name: "Pookie Basket",
    price: "₹1399",
    img: "../../project/photos/forher2.png",
    desc: "a romantic hamper filled with soft, sweet, and thoughtful gifts.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>2 x Crochet Tulip</li>
        <li>1 x Cadbury Bournville</li>
        <li>1 x Tonymoly Sheet Mask Pomegranate</li>
        <li>1 x Tonymoly Sheet Mask Avocado</li>
        <li>1 x The Body Shop Strawberry Shower Gel</li>
      </ul>
    `
  },
  {
    name: "Birthday Bliss Hamper",
    price: "₹1299",
    img: "../../project/photos/bday2.jpg",
    desc: "Make birthdays unforgettable with the Birthday Bliss Hamper - fun and full of surprises.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>2 x beautiful Nail polish</li>
        <li>1 x Body Care Set (Soap / Gel / Lotion)</li>
        <li>1 x Assorted Chocolates</li>
        <li>1 x Cute Purple Scrunchie</li>
      </ul>
    `
  },
  {
    name: "Lavender Care Box",
    price: "₹2399",
    img: "../../project/photos/forher1.jpg",
    desc: "A soothing, heartwarming gift set filled with love, positivity, and relaxation essentials.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>1 x Cozy Fluffy Socks</li>
        <li>1 x Aromatherapy Candle</li>
        <li>1 x Bath Bomb</li>
        <li>1 x Satin Scrunchie</li>
        <li>1 x Greeting Card</li>
      </ul>
    `
  },
  {
    name: "Midnight Elegance Box",
    price: "₹2099",
    img: "../../project/photos/men.png",
    desc: "Dark, classy, and full of charm - the Midnight Elegance Box is designed for those who love sophistication.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>1 x Engraved Tumbler / Premium Mug</li>
        <li>1 x Stylish Watch / Accessory</li>
        <li>1 x Dark Chocolate Pack</li>
      </ul>
    `
  },
  {
    name: "Happiness Basket",
    price: "₹1099",
    img: "../../project/photos/happy.png",
    desc: "A basket full of happiness for kids. Chocolates and a coloring book inside.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>1 x Bournville Dark Chocolate (50%)</li>
        <li>1 x The Body Shop Strawberry Shower Gel</li>
        <li>1 x RENÉE Bloom Perfume</li>
        <li>1 x Cute Smiley Plant Pot with Faux Grass</li>
        <li>2 x Crochet Tulip Flowers</li>
      </ul>
    `
  },
  {
    name: "Tulip Strawberry Bliss Box",
    price: "₹1799",
    img: "../../project/photos/tulip_bliss.png",
    desc: "A delightful hamper with floral, chocolate, skincare, and a cute plant pot for a perfect gift.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>1 x Cadbury Bournville Classic Dark Chocolate (50%)</li>
        <li>1 x The Body Shop Strawberry Shower Gel</li>
        <li>1 x RENÉE Bloom Perfume</li>
        <li>1 x Cute Smiley Plant Pot with Faux Grass</li>
        <li>1 x Skincare / Sheet Mask Pack</li>
        <li>2 x Crochet Tulip Flowers</li>
      </ul>
    `
  }
];

// Attach modal open logic to each view-btn
document.querySelectorAll('.view-btn').forEach((btn, idx) => {
  btn.addEventListener('click', function() {
    const p = products[idx];
    document.getElementById('modalImg').src = p.img;
    document.getElementById('modalImg').alt = p.name;
    document.getElementById('modalTitle').textContent = p.name;
    document.getElementById('modalPrice').textContent = p.price;
    // document.getElementById('modalOriginalPrice').textContent = p.originalPrice;
    document.getElementById('modalDesc').textContent = p.desc;
    document.getElementById('modalIncludes').innerHTML = p.includes;
    document.getElementById('productModal').style.display = 'block';
  });
});

// Attach modal close logic
document.getElementById('modalClose').onclick = function() {
  document.getElementById('productModal').style.display = 'none';
};

// Show toast message
function showCartToast() {
  const cartToast = document.getElementById('cartToast');
  cartToast.style.display = 'block';
  setTimeout(() => {
    cartToast.style.display = 'none';
  }, 2000);
}

// Helper: Send product to cart via AJAX
function addToCartAjax(data) {
  fetch('../../project/cartPage/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data).toString()
  })
  .then(response => response.ok ? showCartToast() : alert('Error adding to cart'));
}

// 🛒 button logic
document.querySelectorAll('.cart-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const productElem = btn.closest('.product');
    const title = productElem.querySelector('h3').textContent;
    const price = productElem.querySelector('.new-price').textContent;
    const imgElem = productElem.querySelector('img');
    const img = imgElem ? imgElem.src : '';

    addToCartAjax({
      add_to_cart: 1,
      name: title,
      price: price,
      img: img
    });
  });
});

// Modal "ADD TO CART" button logic
const modalAddBtn = document.getElementById('modalAdd');
if (modalAddBtn) {
  modalAddBtn.addEventListener('click', function() {
    const title = document.getElementById('modalTitle').textContent;
    const price = document.getElementById('modalPrice').textContent;
    const img = document.getElementById('modalImg').src;
    const desc = document.getElementById('modalDesc').textContent;
    const includes = document.getElementById('modalIncludes').textContent;

    addToCartAjax({
      add_to_cart: 1,
      name: title,
      price: price,
      img: img,
      desc: desc,
      includes: includes
    });
  });
}