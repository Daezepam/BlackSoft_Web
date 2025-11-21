let slides = document.querySelectorAll('.slides img');
let prev = document.querySelector('.Anterior');
let next = document.querySelector('.Siguiente');

let index = 0;

if (slides.length > 0) {

    function showSlide(i) {
        slides.forEach((slide, idx) => {
            slide.classList.toggle('active', idx === i);
        });
    }

    if (prev && next) {
        prev.addEventListener('click', () => {
            index = (index - 1 + slides.length) % slides.length;
            showSlide(index);
        });

        next.addEventListener('click', () => {
            index = (index + 1) % slides.length;
            showSlide(index);
        });
    }

    showSlide(index);
}

const btn = document.querySelector(".btn-categorias");
const lista = document.querySelector(".lista-categorias");

btn.addEventListener("click", () => {
  lista.style.display = lista.style.display === "block" ? "none" : "block";
});

