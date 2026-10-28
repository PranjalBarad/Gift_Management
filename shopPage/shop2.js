
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


// Auto-sort based on ?category= in URL
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

function autoSortFromQuery() {
  const category = getQueryParam('category');
  if (!category) {
    sortProducts('all');
    return;
  }
  // if the select exists and has an option for this category, set it
  const sel = document.getElementById('sortSelect');
  if (sel) {
    // try to set value, if option not present we'll still call sortProducts
    try { sel.value = category; } catch (e) { }
  }
  sortProducts(category);
}

// When the user changes the select, update the URL so server-side filtering can occur.
function changeCategory(category) {
  // update query param and navigate so server-side will return filtered set.
  const url = new URL(window.location.href);
  if (!category || category === 'all') {
    url.searchParams.delete('category');
  } else {
    url.searchParams.set('category', category);
  }
  // Navigate to the updated URL. This reload will trigger server-side filtering
  // while autoSortFromQuery will still run to set the client-side state.
  window.location.href = url.toString();
}

window.addEventListener('DOMContentLoaded', autoSortFromQuery);

//model
// Use server-provided `products` array when available. The server prints a `products` JS array in the page.
// If it's not present for some reason, fallback to an empty array to avoid errors.
var products = (typeof products !== 'undefined' && Array.isArray(products)) ? products : [];

// Attach modal open logic to each view-btn. Use DOM data-index to map to server `products` array.
document.querySelectorAll('.view-btn').forEach((btn) => {
  btn.addEventListener('click', function (e) {
    // Locate product container parent and get data-index
    const productEl = e.target.closest('.product');
    if (!productEl) return;
    const idx = parseInt(productEl.getAttribute('data-index'), 10);
    const p = products[idx] || null;
    if (!p) {
      // as a fallback, try to assemble from DOM
      const imgEl = productEl.querySelector('img');
      const title = productEl.querySelector('h3') ? productEl.querySelector('h3').textContent.trim() : '';
      const price = productEl.getAttribute('data-price') || '';
      document.getElementById('modalImg').src = imgEl ? imgEl.src : '';
      document.getElementById('modalImg').alt = title;
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalPrice').textContent = price;
      document.getElementById('modalDesc').textContent = productEl.getAttribute('data-desc') || '';
      document.getElementById('modalIncludes').innerHTML = '';
      document.getElementById('productModal').style.display = 'block';
      return;
    }
    document.getElementById('modalImg').src = p.img;
    document.getElementById('modalImg').alt = p.name;
    document.getElementById('modalTitle').textContent = p.name;
    document.getElementById('modalPrice').textContent = p.price;
    // store a raw numeric price (from data-price) so modal add uses an unformatted value
    try {
      document.getElementById('modalPrice').dataset.raw = productEl.getAttribute('data-price') || p.price.replace(/[^0-9.]/g, '');
    } catch (e) { }
    document.getElementById('modalDesc').textContent = p.desc;
    // Render includes as a list if it's comma-separated, otherwise preserve existing HTML
    document.getElementById('modalIncludes').innerHTML = renderIncludesToHTML(p.includes || '');
    document.getElementById('productModal').style.display = 'block';
  });
});

// Escape text to HTML
function escapeHtml(unsafe) {
  return unsafe.replace(/[&<>"']/g, function (m) {
    switch (m) {
      case '&': return '&amp;';
      case '<': return '&lt;';
      case '>': return '&gt;';
      case '"': return '&quot;';
      case "'": return '&#039;';
      default: return m;
    }
  });
}

// Convert includes text into HTML. If includes already contains HTML tags, keep it.
function renderIncludesToHTML(includesText) {
  if (!includesText) return '';
  // If there's any HTML tag-like content, return as-is
  if (/<[a-z][\s\S]*>/i.test(includesText)) return includesText;
  const parts = includesText.split(',').map(s => s.trim()).filter(Boolean);
  if (parts.length === 0) return '';
  return '<ul>' + parts.map(p => '<li>' + escapeHtml(p) + '</li>').join('') + '</ul>';
}

// Attach modal close logic
document.getElementById('modalClose').onclick = function () {
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
function addToCartAjax(data, onDone) {
  // disable further adds while pending by returning a promise
  // mark this request as AJAX so server can avoid redirecting
  try { data.ajax = 1; } catch (e) { }
  return fetch('../../project/cartPage/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data).toString()
  })
    .then(response => {
      // If server returned JSON (AJAX), parse it and update UI accordingly
      const contentType = response.headers.get('content-type') || '';
      if (contentType.indexOf('application/json') !== -1) {
        return response.json().then(json => {
          if (json && json.success) {
            showCartToast();
            // intentionally do not update a numeric badge in header
            if (typeof onDone === 'function') onDone(null);
          } else {
            alert(json && json.message ? json.message : 'Error adding to cart');
            if (typeof onDone === 'function') onDone(new Error('add_failed'));
          }
        });
      }
      if (response.redirected) {
        // server redirects to cart.php on non-AJAX add; follow by navigating there
        window.location.href = response.url;
        return Promise.resolve();
      }
      if (response.ok) {
        showCartToast();
        if (typeof onDone === 'function') onDone(null);
      } else {
        alert('Error adding to cart');
        if (typeof onDone === 'function') onDone(new Error('add_failed'));
      }
    }).catch(err => {
      console.error('Add to cart failed', err);
      alert('Unable to add to cart. Try again.');
      if (typeof onDone === 'function') onDone(err);
    });
}

// Helper to get product object by name
function getProductByName(name) {
  return products.find(p => p.name === name);
}

// Helper to convert HTML includes to pipe-separated string
// Helper to convert HTML includes to comma-separated string
function htmlIncludesToComma(includesHtml) {
  const temp = document.createElement('div');
  temp.innerHTML = includesHtml;
  const lis = temp.querySelectorAll('li');
  // join with comma and a space for readability
  return Array.from(lis).map(li => li.textContent.trim()).filter(Boolean).join(',');
}

// 🛒 button logic — cart button logic: map using DOM data-index and prevent double clicks
document.querySelectorAll('.cart-btn').forEach((btn) => {
  let pending = false;
  btn.addEventListener('click', function (e) {
    if (pending) return;
    const productEl = e.target.closest('.product');
    if (!productEl) return;
    const idx = parseInt(productEl.getAttribute('data-index'), 10);
    const p = products[idx] || null;
    // build payload from server product when available, else from DOM
    const payload = { add_to_cart: 1 };
    if (p) {
      payload.name = p.name;
      // prefer the DOM data-price (numeric) when available to avoid formatted strings like "₹5,000"
      payload.price = productEl.getAttribute('data-price') || p.price;
      payload.img = p.img;
      payload.desc = p.desc || '';
      payload.includes = htmlIncludesToComma(p.includes || '');
    } else {
      const imgEl = productEl.querySelector('img');
      payload.name = productEl.querySelector('h3') ? productEl.querySelector('h3').textContent.trim() : '';
      payload.price = productEl.getAttribute('data-price') || '';
      payload.img = imgEl ? imgEl.src : '';
      payload.desc = productEl.getAttribute('data-desc') || '';
      payload.includes = '';
    }
    pending = true;
    btn.classList.add('loading');
    addToCartAjax(payload, function () {
      pending = false;
      btn.classList.remove('loading');
    });
  });
});

// Modal "ADD TO CART" button logic
const modalAddBtn = document.getElementById('modalAdd');
if (modalAddBtn) {
  modalAddBtn.addEventListener('click', function () {
    const title = document.getElementById('modalTitle').textContent;
    // Prefer raw numeric price stored on the modal, fallback to displayed text
    const modalPriceEl = document.getElementById('modalPrice');
    const price = (modalPriceEl && modalPriceEl.dataset && modalPriceEl.dataset.raw) ? modalPriceEl.dataset.raw : (modalPriceEl ? modalPriceEl.textContent : '');
    const img = document.getElementById('modalImg').src;
    const desc = document.getElementById('modalDesc').textContent;
    // Normalize includes inside modal to comma-separated values.
    // Prefer extracting from any rendered <ul>/<li> so lists remain accurate.
    const modalIncludesEl = document.getElementById('modalIncludes');
    let includesCsv = '';
    if (modalIncludesEl) {
      const lis = modalIncludesEl.querySelectorAll('li');
      if (lis.length) {
        includesCsv = Array.from(lis).map(li => li.textContent.trim()).filter(Boolean).join(',');
      } else {
        // fallback: split on newlines or commas
        const rawIncludes = modalIncludesEl.innerText || '';
        includesCsv = rawIncludes.split(/[\r\n,]+/).map(s => s.trim()).filter(Boolean).join(',');
      }
    }

    addToCartAjax({
      add_to_cart: 1,
      name: title,
      price: price,
      img: img,
      desc: desc,
      includes: includesCsv
    });
  });
}

// If a product id is passed in the URL (e.g., ?product=123), auto-scroll to the product,
// give it a temporary highlight, and open the modal programmatically.
function openProductFromQuery() {
  const prodId = getQueryParam('product');
  if (!prodId) return;
  // find product element with matching data-id
  const selector = `.product[data-id="${prodId}"]`;
  const el = document.querySelector(selector);
  if (!el) return;
  // Scroll into view smoothly
  try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) { el.scrollIntoView(); }
  // Add temporary highlight
  el.classList.add('highlighted-product');
  setTimeout(() => el.classList.remove('highlighted-product'), 4000);
  // Trigger view button to open modal (if present)
  const viewBtn = el.querySelector('.view-btn');
  if (viewBtn) {
    // Delay slightly to allow scroll animation to finish
    setTimeout(() => viewBtn.click(), 300);
  }
}

window.addEventListener('DOMContentLoaded', openProductFromQuery);