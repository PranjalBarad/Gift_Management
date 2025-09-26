// Modal functionality
let modal = document.getElementById("modal");
let btn = document.getElementById("addBtn");
let span = document.getElementsByClassName("close")[0];

if(btn){
  btn.onclick = function(){ modal.style.display = "flex"; }
}
if(span){
  span.onclick = function(){ modal.style.display = "none"; }
}
window.onclick = function(event){
  if(event.target == modal){ modal.style.display = "none"; }
}
// Open & Close Modal
function openModal() {
  document.getElementById("productModal").style.display = "flex";
}
function closeModal() {
  document.getElementById("productModal").style.display = "none";
}
