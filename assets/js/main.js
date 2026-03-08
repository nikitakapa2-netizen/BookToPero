document.querySelectorAll('[data-confirm]').forEach((el)=>{
  el.addEventListener('click',(e)=>{ if(!confirm(el.dataset.confirm)){ e.preventDefault(); } });
});

const toTopBtn = document.getElementById('toTopBtn');
if (toTopBtn) {
  window.addEventListener('scroll', () => {
    toTopBtn.classList.toggle('show', window.scrollY > 300);
  });
  toTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

const imageInput = document.querySelector('input[name="image"]');
const preview = document.getElementById('imagePreview');
if (imageInput && preview) {
  const render = () => { preview.src = imageInput.value || 'https://via.placeholder.com/300x400?text=Cover'; };
  imageInput.addEventListener('input', render);
  render();
}
