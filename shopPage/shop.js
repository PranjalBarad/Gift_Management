document.addEventListener('DOMContentLoaded', function () {

  // ----- Sorting / Filtering -----
  window.sortProducts = function(category) {
    const products = document.querySelectorAll('.product');
    let count = 0;
    products.forEach(p => {
      if (category === 'all' || p.classList.contains(category)) {
        p.style.display = ''; // show
        count++;
      } else {
        p.style.display = 'none'; // hide
      }
    });
    document.getElementById('resultsText').textContent = `Showing ${count} results`;
  };
  sortProducts('all');

  // ----- Modal elements -----
  const modal = document.getElementById('productModal');
  const modalImg = document.getElementById('modalImg');
  const modalTitle = document.getElementById('modalTitle');
  const modalPrice = document.getElementById('modalPrice');
  const modalDesc = document.getElementById('modalDesc');
  const modalAdd = document.getElementById('modalAdd');
  const modalClose = document.getElementById('modalClose');
  const toast = document.getElementById('cartToast');

  let currentProductEl = null;

  // Utility: show toast
  function showToast(text = 'Box added to cart successfully.') {
    toast.textContent = '✅ ' + text;
    toast.style.display = 'block';
    toast.setAttribute('aria-hidden','false');
    setTimeout(() => {
      toast.style.display = 'none';
      toast.setAttribute('aria-hidden','true');
    }, 2400);
  }

  // Open modal for a product element
  function openModalForProduct(productEl) {
    currentProductEl = productEl;
    const imgSrc = productEl.querySelector('.product-img img').src;
    const title = productEl.querySelector('h3').innerText;
    const price = productEl.querySelector('.new-price').innerText;
    // optional: description if you add later
    const desc = productEl.dataset.desc || '';

    modalImg.src = imgSrc;
    modalTitle.textContent = title;
    modalPrice.textContent = price;
    modalDesc.textContent = desc;
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden','false');
  }

  // Close modal
  function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
  }

  // click handlers using delegation inside product grid
  const grid = document.getElementById('productGrid');
  grid.addEventListener('click', function (e) {
    const viewBtn = e.target.closest('.view-btn');
    const cartBtn = e.target.closest('.cart-btn');

    if (viewBtn) {
      const product = viewBtn.closest('.product');
      openModalForProduct(product);
      return;
    }

    if (cartBtn) {
      // add to cart direct from card
      const name = cartBtn.dataset.name || cartBtn.closest('.product').querySelector('h3').innerText;
      showToast(`${name} added to cart successfully.`);
      // OPTIONAL: save to localStorage here
      return;
    }
  });

  // modal Add to Cart
  modalAdd.addEventListener('click', function () {
    if (currentProductEl) {
      const name = currentProductEl.querySelector('h3').innerText;
      showToast(`${name} added to cart successfully.`);
      // OPTIONAL: save to localStorage here
    }
    closeModal();
  });

  // close modal events
  modalClose.addEventListener('click', closeModal);
  window.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });

});


// Add this inside your modalAdd click handler in shop2.js
document.getElementById('modalAdd').onclick = function() {
  const name = document.getElementById('modalTitle').textContent;
  const price = document.getElementById('modalPrice').textContent;
  const img = document.getElementById('modalImg').src;

  // Create a form and submit to cart.php
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '../../project/cartPage/cart.php';

  form.innerHTML = `
    <input type="hidden" name="add_to_cart" value="1">
    <input type="hidden" name="name" value="${name}">
    <input type="hidden" name="price" value="${price}">
    <input type="hidden" name="img" value="${img}">
  `;
  document.body.appendChild(form);
  form.submit();
};