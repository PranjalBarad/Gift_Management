function filterProducts(category) {
  let products = document.querySelectorAll('.product');
  products.forEach(product => {
    if (category === 'all') {
      product.style.display = 'block'; // show all
    } else {
      if (product.classList.contains(category)) {
        product.style.display = 'block'; // show selected
      } else {
        product.style.display = 'none'; // hide others
      }
    }
  });
}

// Show default products when page loads
filterProducts('all');