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
    img: "../../project/photos/FOR HIM 1.png",
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
    name: "Midnight Elegance Box",
    price: "₹2399",
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
    name: "The Joyful Munch",
    price: "₹899",
    img: "../../project/photos/joyful.png",
    desc: "Bring a burst of joy to your celebrations, a delightful assortment of sweet treats crafted to satisfy every craving.",
    includes: `
      <strong>Hamper Includes</strong>
      <ul>
        <li>1 X Personalized White Photo Frame</li>
        <li>1 X Snackible Ragi Chips</li>
        <li>1 X 4 Metal Tealight Candle</li>
        <li>1 X Happy Diwali Card</li>
      </ul>
    `
  },
  {
    name: "Happiness Basket",
    price: "₹2399",
    img: "../../project/photos/happy.png",
    desc: "A basket full of happiness for kids. Chocolates and a coloring book inside.",
    includes: `
      <strong>Includes:</strong>
      <ul>
        <li>Chocolates</li>
        <li>Coloring Book</li>
        <li>2 x beautiful Nail polish</li>
        <li>1 x Body Care Set (Soap / Gel / Lotion)</li>
        <li>1 x Assorted Chocolates</li>
        <li>1 x Cute Purple Scrunchie</li>
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


document.getElementById('modalAdd').onclick = function() {
  document.getElementById('cartToast').style.display = 'block';
  setTimeout(() => {
    document.getElementById('cartToast').style.display = 'none';
  }, 2000);
};



// Add this inside your modalAdd click handler in shop2.js
document.getElementById('modalAdd').onclick = function() {
  const name = document.getElementById('modalTitle').textContent;
  const price = document.getElementById('modalPrice').textContent;
  const img = document.getElementById('modalImg').src;
  const desc = document.getElementById('modalDesc').textContent;
  // For includes, join list items with "|" for PHP to split
  const includes = Array.from(document.querySelectorAll('#modalIncludes ul li')).map(li => li.textContent).join('|');

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '../../project/cartPage/cart.php';

  form.innerHTML = `
    <input type="hidden" name="add_to_cart" value="1">
    <input type="hidden" name="name" value="${name}">
    <input type="hidden" name="price" value="${price}">
    <input type="hidden" name="img" value="${img}">
    <input type="hidden" name="desc" value="${desc}">
    <input type="hidden" name="includes" value="${includes}">
  `;
  document.body.appendChild(form);
  form.submit();
};