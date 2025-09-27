// You can add JS for cart page here if needed (e.g., confirmation before remove)
document.querySelectorAll('.remove-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    if (!confirm('Remove this item from cart?')) {
      e.preventDefault();
    }
  });
});
