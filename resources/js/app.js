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
