/* ================= CARRUSEL ================= */

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

/* ================= CATEGORÍAS (Solo si existen) ================= */

const btnCategorias = document.querySelector(".btn-categorias");
const listaCategorias = document.querySelector(".lista-categorias");

if (btnCategorias && listaCategorias) {
    btnCategorias.addEventListener("click", () => {
        listaCategorias.style.display =
            listaCategorias.style.display === "block" ? "none" : "block";
    });
}

/* ================= BUSCADOR ================= */

document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("searchbtn");
    const input = document.getElementById("busca");
    const container = document.querySelector(".search");

    if (!btn || !input || !container) {
        console.error("Elemento del buscador no encontrado.");
        return;
    }

    console.log("btn:", btn);
    console.log("input:", input);

    btn.addEventListener("click", function () {

        // Toggle visual
        container.classList.toggle("active");

        // Mostrar input
        input.classList.toggle("activo");

        // Enfocar cuando se despliegue
        if (input.classList.contains("activo")) {
            input.focus();
        }
    });

    input.addEventListener("keypress", function (e) {
        if (e.key === "Enter" && input.value.trim() !== "") {
            console.log("Buscando:", input.value);
        }
    });
});

// -------- MODO OSCURO --------
const themeBtn = document.getElementById("themeToggle");
const html = document.documentElement;

if (localStorage.getItem("theme") === "dark") {
    html.setAttribute("data-theme", "dark");
}

themeBtn.onclick = function() {
    const current = html.getAttribute("data-theme");
    if (current === "dark") {
        html.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
    } else {
        html.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
    }
};
