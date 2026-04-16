window.addEventListener("load", () => {
  document.querySelector(".hero-section").classList.add("show");
});

window.addEventListener("load", () => {
  document.body.classList.add("page-loaded");
});

document.querySelectorAll(".contact-card").forEach((card) => {
  card.addEventListener("mouseenter", function () {
    this.style.transform = "translateY(-5px)";
  });

  card.addEventListener("mouseleave", function () {
    this.style.transform = "translateY(0)";
  });
});
/* About Animations */
const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("visible");
    }
  });
}, observerOptions);
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".fade-in").forEach((el) => {
    observer.observe(el);
  });
});
