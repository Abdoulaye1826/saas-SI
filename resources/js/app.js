import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Boutique en ligne — révèle progressivement les éléments [data-reveal] au
 * scroll (voir resources/css/app.css). Pas de dépendance : un seul
 * IntersectionObserver partagé.
 *
 * Ce script est chargé via @vite en <script type="module">, qui s'exécute
 * après l'analyse complète du DOM — donc toujours APRÈS que
 * `DOMContentLoaded` s'est déjà déclenché. Un `addEventListener('DOMContentLoaded', ...)`
 * ici ne se déclencherait donc jamais : le code s'exécute directement, le
 * DOM est déjà prêt par construction.
 */
(function revealOnScroll() {
    const targets = document.querySelectorAll('[data-reveal]');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    targets.forEach((el) => observer.observe(el));

    // Filet de sécurité : le contenu ne doit jamais rester invisible en
    // permanence si l'observateur ne se déclenche pas pour une raison
    // quelconque (navigateur ancien, page chargée hors écran...) — la
    // décoration ne doit jamais prendre le pas sur l'accès au contenu.
    setTimeout(() => {
        targets.forEach((el) => el.classList.add('is-visible'));
        observer.disconnect();
    }, 2500);
})();

/**
 * Effet de profondeur (parallax) très léger sur le hero de la boutique
 * (brief §4) : le fond, l'illustration et les particules se déplacent à
 * des vitesses légèrement différentes au scroll. Purement décoratif,
 * jamais sur mobile (où le hero occupe tout l'écran, l'effet ne se verrait
 * quasiment pas) et jamais si l'utilisateur préfère moins de mouvement.
 */
(function heroParallax() {
    const layers = document.querySelectorAll('[data-parallax]');
    if (!layers.length) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || window.innerWidth < 640) return;

    let ticking = false;

    function update() {
        const scrollY = window.scrollY;
        layers.forEach((el) => {
            const speed = parseFloat(el.dataset.parallax) || 0.15;
            // La section hero peut avoir défilé hors écran : au-delà, on
            // fige le décalage plutôt que de continuer à le calculer.
            const offset = Math.min(scrollY, 900) * speed;
            el.style.transform = `translate3d(0, ${offset}px, 0)`;
        });
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });
})();

/**
 * Barre de chargement fine en haut de page (brief §13) : rejouée à chaque
 * navigation classique (elle est injectée côté serveur dans le layout,
 * voir layouts/storefront.blade.php) et retirée du DOM une fois son
 * animation CSS terminée, pour ne jamais laisser un élément mort derrière.
 */
(function removeLoadingBarWhenDone() {
    const bar = document.getElementById('storeLoadingBar');
    if (!bar) return;
    bar.addEventListener('animationend', () => bar.remove());
    setTimeout(() => bar.remove(), 1500);
})();
