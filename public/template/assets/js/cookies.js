document.addEventListener("DOMContentLoaded", () => {
  const choice = localStorage.getItem("cookies-choice");

  // Affiche le bandeau si aucun choix
  if (!choice) {
    showCookieBanner();
  } else if (choice === "accepted") {
    loadTrackingScripts();
  }

  // Gestion du bouton "Gérer mes cookies" dans le footer
  const manageBtn = document.getElementById("manage-cookies");
  if (manageBtn) {
    manageBtn.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem("cookies-choice"); // efface le choix
      location.reload(); // recharge la page pour réafficher le bandeau
    });
  }
});

// Fonction qui crée le bandeau
function showCookieBanner() {
  const banner = document.createElement("div");
  banner.id = "cookie-banner";
  banner.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    max-width: 480px;
    background: rgba(0, 0, 0, 0.9);
    color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    font-family: 'Arial', sans-serif;
    font-size: 14px;
    text-align: center;
    opacity: 0;
    animation: fadeIn 0.6s forwards;
  `;

banner.innerHTML = `
  <p style="margin-bottom:12px;">
    Ce site utilise des cookies pour analyser le trafic.<br>
  <a href="https://allaboutcookies.org/fr" class="tt-link" style="color:#ff0000; font-weight:bold; text-decoration:underline;" target="_blank">
    En savoir plus sur les cookies
  </a>

  </p>
  <div>
    <button id="accept-cookies" style="background:#4CAF50;color:white;border:none;padding:10px 18px;border-radius:8px;cursor:pointer;margin-right:8px;font-weight:bold;">Accepter</button>
    <button id="reject-cookies" style="background:#f44336;color:white;border:none;padding:10px 18px;border-radius:8px;cursor:pointer;font-weight:bold;">Refuser</button>
  </div>
`;



  document.body.appendChild(banner);

  // Gestion des boutons
  document.getElementById("accept-cookies").addEventListener("click", () => {
    localStorage.setItem("cookies-choice", "accepted");
    banner.remove();
    loadTrackingScripts();
  });

  document.getElementById("reject-cookies").addEventListener("click", () => {
    localStorage.setItem("cookies-choice", "rejected");
    banner.remove();
  });
}

// Fonction qui charge Google Analytics uniquement si accepté
function loadTrackingScripts() {
  const gaScript = document.createElement("script");
  gaScript.async = true;
  gaScript.src = "https://www.googletagmanager.com/gtag/js?id=G-H9C6F8VG4D";
  document.head.appendChild(gaScript);

  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-H9C6F8VG4D', { 'anonymize_ip': true });
}

// Ajoute l'animation fadeIn (si tu n'as pas de CSS externe)
const style = document.createElement('style');
style.textContent = `
@keyframes fadeIn {
  to { opacity: 1; }
}
`;
document.head.appendChild(style);
