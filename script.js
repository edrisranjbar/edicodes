// Year
document.getElementById("year").textContent = new Date().getFullYear();

// Navbar background on scroll
const nav = document.getElementById("nav");
const onScroll = () => nav.classList.toggle("scrolled", window.scrollY > 10);
onScroll();
window.addEventListener("scroll", onScroll, { passive: true });

// Mobile menu
const burger = document.getElementById("burger");
const menu = document.getElementById("mobileMenu");
const toggleMenu = (open) => {
  const isOpen = open ?? !menu.classList.contains("open");
  menu.classList.toggle("open", isOpen);
  burger.classList.toggle("open", isOpen);
  burger.setAttribute("aria-expanded", String(isOpen));
  menu.setAttribute("aria-hidden", String(!isOpen));
  document.body.style.overflow = isOpen ? "hidden" : "";
};
burger.addEventListener("click", () => toggleMenu());
menu.querySelectorAll("a").forEach((a) => a.addEventListener("click", () => toggleMenu(false)));

// Reveal on scroll
const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("in");
        revealObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.12 }
);
document.querySelectorAll(".reveal").forEach((el, i) => {
  el.style.transitionDelay = `${(i % 4) * 80}ms`;
  revealObserver.observe(el);
});

// Animate skill bars when visible
const bars = document.getElementById("bars");
if (bars) {
  const barObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.querySelectorAll(".bar").forEach((bar, idx) => {
          const level = parseInt(bar.dataset.level || "0", 10);
          const fill = bar.querySelector("i");
          setTimeout(() => { fill.style.width = level * 10 + "%"; }, idx * 120);
        });
        barObserver.unobserve(entry.target);
      });
    },
    { threshold: 0.3 }
  );
  barObserver.observe(bars);
}
