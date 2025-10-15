let index = 0;
const slides = document.querySelectorAll('.slides img');
const prev = document.querySelector('.Anterior');
const next = document.querySelector('.Siguiente');

function showSlide(i) {
  slides.forEach((slide, idx) => {
    slide.classList.toggle('active', idx === i);
  });
}

prev.addEventListener('click', () => {
  index = (index - 1 + slides.length) % slides.length;
  showSlide(index);
});

next.addEventListener('click', () => {
  index = (index + 1) % slides.length;
  showSlide(index);
});

showSlide(index);
