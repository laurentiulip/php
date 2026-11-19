'use strict';
// Meniul folosește dialogul nativ: focusul rămâne în panou, Escape îl închide.
const drawer = document.querySelector('#meniu-lateral');
const openMenu = document.querySelector('#deschide-meniu');
const closeMenu = document.querySelector('#inchide-meniu');
if (drawer && openMenu && closeMenu) {
  const close = () => drawer.close();
  openMenu.addEventListener('click', () => {
    drawer.showModal();
    openMenu.setAttribute('aria-expanded', 'true');
    document.body.classList.add('menu-open');
    closeMenu.focus();
  });
  closeMenu.addEventListener('click', close);
  drawer.addEventListener('click', (event) => {
    const rect = drawer.getBoundingClientRect();
    if (event.clientX > rect.right || event.clientX < rect.left || event.clientY < rect.top || event.clientY > rect.bottom) close();
  });
  drawer.addEventListener('close', () => {
    openMenu.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('menu-open');
    openMenu.focus();
  });
}
// Săgețile și punctele afișează aceeași imagine activă, fără avans automat.
const carousel = document.querySelector('.carousel');
if (carousel) {
  const slides = [...carousel.querySelectorAll('[data-slide]')];
  const dots = [...carousel.querySelectorAll('[data-dot]')];
  let current = 0;
  const showSlide = (index) => {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => { slide.hidden = i !== current; });
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === current);
      dot.setAttribute('aria-pressed', String(i === current));
    });
    carousel.querySelector('.slide-count').textContent = `${String(current + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    carousel.querySelector('[data-carousel-status]').textContent = `Imaginea ${current + 1} din ${slides.length}`;
  };
  carousel.querySelector('[data-prev]').addEventListener('click', () => showSlide(current - 1));
  carousel.querySelector('[data-next]').addEventListener('click', () => showSlide(current + 1));
  dots.forEach((dot, i) => dot.addEventListener('click', () => showSlide(i)));
  carousel.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') { event.preventDefault(); showSlide(current - 1); }
    if (event.key === 'ArrowRight') { event.preventDefault(); showSlide(current + 1); }
  });
}

document.querySelectorAll('form[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (event) => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
