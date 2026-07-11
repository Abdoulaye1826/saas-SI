/**
 * Mboup Gaming SI — Keep-alive de session + détection propre d'expiration
 *
 * Objectif : une boutique laisse l'application ouverte toute une journée de
 * travail. Sans ce script, une session inactive finit par expirer et la
 * moindre action (soumission d'un formulaire, envoi WhatsApp...) déclenche
 * une erreur 419 brute.
 *
 *  - Tant que l'onglet est visible, on ping /keep-alive à intervalle régulier
 *    pour empêcher l'expiration silencieuse de la session.
 *  - On intercepte globalement les réponses fetch() en 419/401 (session
 *    réellement expirée, ou compte désactivé) pour afficher un message
 *    clair puis rediriger vers la connexion — au lieu de laisser l'action
 *    échouer silencieusement ou afficher une page d'erreur brute.
 */
(function () {
    'use strict';

    if (!window.APP_KEEP_ALIVE_URL || !window.APP_LOGIN_URL) {
        return;
    }

    const KEEP_ALIVE_URL = window.APP_KEEP_ALIVE_URL;
    const LOGIN_URL = window.APP_LOGIN_URL;
    const PING_INTERVAL_MS = 5 * 60 * 1000; // 5 minutes

    let redirecting = false;

    function goToLogin() {
        if (redirecting) return;
        redirecting = true;

        if (window.UiToast) {
            window.UiToast.show('Votre session a expiré. Veuillez vous reconnecter.', 'error');
        }

        setTimeout(function () {
            window.location.href = LOGIN_URL;
        }, window.UiToast ? 1200 : 0);
    }

    /* ── Interception globale des réponses fetch() ───────────────── */
    const originalFetch = window.fetch.bind(window);
    window.fetch = function (...args) {
        return originalFetch(...args).then(function (response) {
            if (!redirecting && (response.status === 419 || response.status === 401)) {
                goToLogin();
            }
            return response;
        });
    };

    /* ── Keep-alive périodique (uniquement onglet visible) ───────── */
    function ping() {
        if (redirecting || document.visibilityState !== 'visible') return;

        // fetch() est déjà intercepté ci-dessus : un 419/401 ici déclenche
        // automatiquement goToLogin().
        window.fetch(KEEP_ALIVE_URL, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        }).catch(function () {
            // Coupure réseau ponctuelle : on retentera au prochain intervalle.
        });
    }

    setInterval(ping, PING_INTERVAL_MS);
})();
