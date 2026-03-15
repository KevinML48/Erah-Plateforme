@extends('marketing.layouts.template')


@section('title', 'Boutique ERAH | T-shirt officiel floqué')

@section('meta_description', 'Découvrez le T-shirt officiel ERAH Esport : coupe premium, flocage personnalisé, prêt à être expédié.')

@section('meta_keywords', 'Boutique ERAH, T-shirt esport, merch ERAH, flocage personnalisé, maillot ERAH')

@section('meta_author', 'ERAH Esport')

@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')


@section('page_styles')
@verbatim

<style>
  /* Bandeau cookies stylé */
  #cookie-banner {
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
  }

  /* Animation */
  @keyframes fadeIn {
    to { opacity: 1; }
  }

  /* Boutons stylés */
  #cookie-banner button {
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s, background 0.2s;
  }

  #cookie-banner button#accept-cookies {
    background: #4CAF50;
    color: #fff;
  }

  #cookie-banner button#accept-cookies:hover {
    transform: scale(1.05);
    background: #45a049;
  }

  #cookie-banner button#reject-cookies {
    background: #f44336;
    color: #fff;
  }

  #cookie-banner button#reject-cookies:hover {
    transform: scale(1.05);
    background: #d7372a;
  }

  @media (max-width: 500px) {
    #cookie-banner div {
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;
    }
    #cookie-banner div button {
      width: 100%;
    }
  }

  /* Boutique Premium Design */
  /* Dark mode (default) */
  :root {
    --shop-accent: #d00000;
    --shop-accent-hover: #ff1a1a;
    --shop-bg-main: #050505;
    --shop-bg-hero: radial-gradient(circle at 15% 50%, rgba(208, 0, 0, 0.15), transparent 40%),
                    radial-gradient(circle at 85% 30%, rgba(50, 50, 50, 0.2), transparent 40%),
                    #050505;
    --shop-card-bg: rgba(20, 20, 20, 0.6);
    --shop-border: rgba(255, 255, 255, 0.08);
    --shop-glass: blur(20px);
    --shop-text-primary: #ffffff;
    --shop-text-secondary: rgba(255, 255, 255, 0.8);
    --shop-text-muted: rgba(255, 255, 255, 0.6);
    --shop-badge-bg: rgba(255, 255, 255, 0.03);
    --shop-mockup-bg: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.05), transparent 70%);
    --shop-image-bg: #101010;
    --shop-overlay-bg: linear-gradient(to top, rgba(0,0,0,0.9), transparent 60%);
    --shop-pattern-opacity: 0.5;
    --shop-pattern-color: %23ffffff;
    --shop-shadow: rgba(0, 0, 0, 0.6);
  }

  /* Light mode */
  .tt-light-theme {
    --shop-bg-main: #f8f8f8;
    --shop-bg-hero: radial-gradient(circle at 15% 50%, rgba(208, 0, 0, 0.08), transparent 40%),
                    radial-gradient(circle at 85% 30%, rgba(200, 200, 200, 0.15), transparent 40%),
                    #f8f8f8;
    --shop-card-bg: rgba(255, 255, 255, 0.9);
    --shop-border: rgba(0, 0, 0, 0.1);
    --shop-text-primary: #1a1a1a;
    --shop-text-secondary: rgba(0, 0, 0, 0.8);
    --shop-text-muted: rgba(0, 0, 0, 0.6);
    --shop-badge-bg: rgba(0, 0, 0, 0.04);
    --shop-mockup-bg: radial-gradient(circle at 50% 50%, rgba(0, 0, 0, 0.03), transparent 70%);
    --shop-image-bg: #e8e8e8;
    --shop-overlay-bg: linear-gradient(to top, rgba(255,255,255,0.95), transparent 60%);
    --shop-pattern-opacity: 0.3;
    --shop-pattern-color: %23000000;
    --shop-shadow: rgba(0, 0, 0, 0.15);
  }

  .tt-tilt-effect {
    perspective: 1000px;
    transform-style: preserve-3d;
  }
  
  .tt-tilt-content {
    will-change: transform;
    transform-style: preserve-3d;
  }

  .shop-hero {
    position: relative;
    background: var(--shop-bg-hero);
    overflow: hidden;
  }

  .shop-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='var(--shop-pattern-color)' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: var(--shop-pattern-opacity);
    pointer-events: none;
  }

  .shop-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1fr 0.9fr;
    gap: 60px;
    align-items: center;
  }

  .shop-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 30px 0 40px;
  }

  .shop-badge {
    padding: 8px 16px;
    border: 1px solid var(--shop-border);
    border-radius: 100px;
    background: var(--shop-badge-bg);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
    color: var(--shop-text-secondary);
  }

  .shop-product-card {
    position: relative;
    background: var(--shop-card-bg);
    border: 1px solid var(--shop-border);
    border-radius: 30px;
    padding: 30px;
    backdrop-filter: var(--shop-glass);
    box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.7);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
  }

  .shop-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 50px 120px -20px rgba(208, 0, 0, 0.15);
    border-color: rgba(255, 255, 255, 0.15);
  }

  .shop-product-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
  }

  .shop-product-tag {
    padding: 6px 12px;
    border-radius: 6px;
    background: var(--shop-accent);
    color: #fff;
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
  }

  .shop-price {
    font-family: 'Big Shoulders Display', sans-serif;
    font-size: 42px;
    font-weight: 800;
    color: var(--shop-text-primary);
  }

  .shop-mockup {
    position: relative;
    background: var(--shop-mockup-bg);
    border-radius: 20px;
    padding: 40px;
    min-height: 460px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
    overflow: hidden;
  }

  .shop-mockup-images {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .shop-mockup-img {
    display: none;
    width: 100%;
    max-width: 420px;
    filter: drop-shadow(0 30px 60px var(--shop-shadow));
    transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
  }

  .shop-mockup-img.active {
    display: block;
    animation: fadeScale 0.5s ease forwards;
  }

  @keyframes fadeScale {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  /* Removed CSS hover transform to let GSAP handle it */

  .shop-controls {
    margin-top: 25px;
    display: flex;
    gap: 6px;
    background: var(--shop-badge-bg);
    backdrop-filter: blur(10px);
    padding: 5px;
    border-radius: 100px;
    border: 1px solid var(--shop-border);
    z-index: 10;
  }

  .shop-control-btn {
    background: transparent;
    border: none;
    color: var(--shop-text-muted);
    padding: 8px 20px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .shop-control-btn:hover {
    color: var(--shop-text-primary);
    background: var(--shop-badge-bg);
  }

  .shop-control-btn.active {
    background: var(--shop-accent);
    color: #fff;
    box-shadow: 0 4px 15px rgba(208, 0, 0, 0.3);
  }

  /* Section showcase T-shirt */
  .shop-tshirt-showcase {
    position: relative;
  }

  .shop-tshirt-image-wrapper {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: var(--shop-image-bg);
    border: 1px solid var(--shop-border);
    aspect-ratio: 4/5;
    box-shadow: 0 20px 40px var(--shop-shadow);
    transition: all 0.4s ease;
  }

  .shop-tshirt-image-wrapper:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 60px rgba(208, 0, 0, 0.2);
    border-color: rgba(208, 0, 0, 0.3);
  }

  .shop-tshirt-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* Removed CSS transition/transform to let GSAP handle it */
  }

  .shop-tshirt-overlay {
    position: absolute;
    inset: 0;
    background: var(--shop-overlay-bg);
    display: flex;
    align-items: flex-end;
    padding: 30px;
    opacity: 1;
  }

  .shop-tshirt-label {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .shop-tshirt-détails {
    margin-top: 20px;
    text-align: center;
  }

  .shop-tshirt-détails h4 {
    margin: 0 0 8px;
    font-family: 'Big Shoulders Display', sans-serif;
    font-size: 28px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: var(--shop-text-primary);
  }

  .shop-tshirt-détails p {
    font-size: 14px;
    color: var(--shop-text-muted);
    line-height: 1.6;
  }

  .shop-cta-row {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 30px;
  }

  .shop-usp {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  }

  .shop-usp-card {
    padding: 24px;
    border-radius: 20px;
    border: 1px solid var(--shop-border);
    background: var(--shop-badge-bg);
    transition: background 0.3s ease;
  }

  .shop-usp-card:hover {
    background: rgba(255, 255, 255, 0.04);
  }

  .shop-usp-card h5 {
    margin: 0 0 10px;
    font-size: 18px;
    font-weight: 600;
    color: var(--shop-text-primary);
  }

  .shop-usp-card p {
    margin: 0;
    font-size: 14px;
    color: var(--shop-text-muted);
    line-height: 1.6;
  }

  .shop-size-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 50px;
  }

  .shop-size-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--shop-border);
  }

  .shop-size-table th,
  .shop-size-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--shop-border);
    text-align: left;
    font-size: 14px;
  }

  .shop-size-table th {
    background: var(--shop-badge-bg);
    font-weight: 700;
    color: var(--shop-text-primary);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.05em;
  }

  .shop-size-table tr:last-child td {
    border-bottom: none;
  }

  /* ============================================
     NOUVELLES CLASSES POUR LE GUIDE DES TAILLES RESPONSIVE
     ============================================ */

  .shop-size-cards {
    display: none; /* Masqué par défaut, affiché sur mobile */
  }

  .shop-size-card {
    background: var(--shop-card-bg);
    border: 1px solid var(--shop-border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
  }

  .shop-size-card:hover {
    border-color: var(--shop-accent);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  }

  .shop-size-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--shop-border);
  }

  .shop-size-card-header h4 {
    font-size: 24px;
    font-weight: 800;
    color: var(--shop-text-primary);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .shop-size-badge {
    background: var(--shop-accent);
    color: #000;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .shop-size-card-measures {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .shop-measure-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .shop-measure-label {
    font-size: 11px;
    color: var(--shop-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
  }

  .shop-measure-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--shop-text-primary);
  }

  /* ============================================
     BOUTON TELECHARGEMENT PDF
     ============================================ */

  .shop-pdf-download {
    text-align: center;
    margin: 20px 0;
  }

  .shop-pdf-download .tt-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .shop-pdf-download .tt-btn i {
    font-size: 16px;
    transition: transform 0.2s ease;
  }

  .shop-pdf-download .tt-btn:hover i {
    transform: scale(1.1);
  }

  .shop-size-table-desktop {
    display: block; /* Affiché par défaut, masqué sur mobile */
  }

  .shop-stamp {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 100px;
    background: rgba(40, 200, 60, 0.1);
    border: 1px solid rgba(40, 200, 60, 0.2);
    color: #4ade80;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .shop-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
  }

  .shop-gallery figure {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--shop-border);
    aspect-ratio: 16/9;
    cursor: pointer;
  }

  .shop-gallery img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .shop-gallery figure:hover img {
    transform: scale(1.05);
  }

  .shop-gallery figcaption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: var(--shop-overlay-bg);
    color: var(--shop-text-primary);
    font-weight: 600;
    font-size: 14px;
    transform: translateY(100%);
    transition: transform 0.3s ease;
  }

  .shop-gallery figure:hover figcaption {
    transform: translateY(0);
  }

  .shopify-buy-box {
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    padding: 22px;
    background: linear-gradient(150deg, rgba(208,0,0,0.14), rgba(255,255,255,0.04));
    box-shadow: 0 24px 60px rgba(0,0,0,0.4);
  }

  .shopify-buy-box h3 {
    margin: 0 0 12px;
  }

  .shopify-buy-box .shopify-buy__product {
    background: transparent;
  }

  .shopify-buy-box .shopify-buy__btn {
    width: 100%;
    height: 58px;
    border: none;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border-radius: 14px !important;
    background: linear-gradient(135deg, #ff2b2b, #d00000);
    box-shadow: 0 18px 40px rgba(208, 0, 0, 0.35);
    font-weight: 800;
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    margin-top: 20px;
  }

  .shopify-buy-box .shopify-buy__btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 22px 50px rgba(255, 43, 43, 0.38);
    filter: brightness(1.05);
  }

  .shopify-buy-box .shopify-buy__option-select {
    background: #0f0f12;
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 12px;
    padding: 10px 12px;
  }

  .shopify-price-row {
    display: flex;
    align-items: baseline;
    gap: 10px;
    margin-bottom: 12px;
  }

  .shopify-price {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.01em;
  }

  /* Drawer Shopify */
  .shopify-buy__cart {
    background: linear-gradient(180deg, #0b0b0d 0%, #0d0d10 100%) !important;
    color: #ffffff !important;
    border-left: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px 0 0 18px;
    box-shadow: -12px 0 40px rgba(0,0,0,0.55);
    max-width: 420px;
    width: 100%;
    padding-bottom: 18px;
    backdrop-filter: blur(8px);
  }

  /* Force la couleur blanche pour tous les éléments de texte dans le panier */
  .shopify-buy__cart,
  .shopify-buy__cart *,
  .shopify-buy__cart a,
  .shopify-buy__cart a:hover,
  .shopify-buy__cart a:focus,
  .shopify-buy__cart a:visited {
    color: #ffffff !important;
  }

  /* Modal Checkout */
  #shopify-checkout-modal {
    animation: fadeIn 0.3s ease;
  }

  #shopify-checkout-modal > div {
    animation: slideUp 0.3s ease;
  }

  @keyframes slideUp {
    from {
      transform: translateY(30px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @media (max-width: 768px) {
    #shopify-checkout-modal > div {
      width: 100%;
      height: 100vh;
      max-width: 100%;
      border-radius: 0;
    }
    
    #close-checkout-modal {
      top: 10px;
      right: 10px;
    }
  }

  .shopify-buy__cart .shopify-buy__cart-item {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .shopify-buy__cart .shopify-buy__cart__header {
    padding: 16px 18px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .shopify-buy__cart .shopify-buy__cart-title {
    color: #ffffff !important;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 14px;
  }

  .shopify-buy__cart .shopify-buy__cart-item__image {
    width: 80px !important;
    height: 80px !important;
    border-radius: 12px;
    object-fit: contain;
    background: #111;
    padding: 4px;
    border: 2px solid rgba(255,255,255,0.1);
  }

  .shopify-buy__cart .shopify-buy__cart-item__title,
  .shopify-buy__cart .shopify-buy__cart-item__price,
  .shopify-buy__cart .shopify-buy__cart-item__variant-title,
  .shopify-buy__cart .shopify-buy__cart-item__quantity {
    color: #ffffff !important;
  }

  /* Tous les textes dans les items du panier */
  .shopify-buy__cart .shopify-buy__cart-item,
  .shopify-buy__cart .shopify-buy__cart-item * {
    color: #ffffff !important;
  }

  /* Exception pour les boutons et inputs qui ont déjà leurs styles */
  .shopify-buy__cart .shopify-buy__cart-item button,
  .shopify-buy__cart .shopify-buy__cart-item input[type="text"],
  .shopify-buy__cart .shopify-buy__cart-item input[type="number"] {
    color: #ffffff !important;
  }

  .shopify-buy__cart .shopify-buy__cart-item__variant-title {
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .shopify-buy__cart .shopify-buy__cart-item__price {
    display: block;
    text-align: right;
    font-weight: 700;
    color: #ffffff !important;
  }

  .shopify-buy__cart .shopify-buy__cart__footer {
    background: #0b0b0d !important;
    border-top: 1px solid rgba(255,255,255,0.08);
  }

  .shopify-buy__cart .shopify-buy__cart__subtotal {
    color: #ffffff !important;
    font-weight: 800 !important;
  }

  .shopify-buy__cart .shopify-buy__cart__notice {
    color: rgba(255,255,255,0.65) !important;
  }

  .shopify-buy__quantity-container,
  .shopify-buy__cart .shopify-buy__cart-item__quantity {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    background: #121214;
    padding: 2px 6px;
  }

  .shopify-buy__quantity-decrement,
  .shopify-buy__quantity-increment,
  .shopify-buy__cart .shopify-buy__quantity-decrement,
  .shopify-buy__cart .shopify-buy__quantity-increment,
  .shopify-buy__cart .shopify-buy__quantity-button {
    background: transparent;
    color: #ffffff;
    border: none;
    width: 26px;
    height: 26px;
    border-radius: 8px;
    transition: background 0.2s ease;
  }

  .shopify-buy__quantity-decrement:hover,
  .shopify-buy__quantity-increment:hover,
  .shopify-buy__cart .shopify-buy__quantity-decrement:hover,
  .shopify-buy__cart .shopify-buy__quantity-increment:hover {
    background: rgba(255,255,255,0.08);
  }

  .shopify-buy__cart .shopify-buy__quantity-input,
  .shopify-buy__quantity-input {
    background: transparent !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    border: none !important;
    width: 32px !important;
    text-align: center;
    font-weight: 700;
  }

  .shopify-buy__cart .shopify-buy__quantity-input:disabled,
  .shopify-buy__quantity-input:disabled {
    background: transparent !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    opacity: 1 !important;
  }

  .shopify-buy__cart .shopify-buy__cart-item__variant-title {
    opacity: 0.7;
  }

  .shopify-buy__cart .shopify-buy__cart-item__quantity {
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    background: #111;
    color: #ffffff !important;
  }

  /* Styles supplémentaires pour tous les éléments de quantité */
  .shopify-buy__cart .shopify-buy__cart-item__quantity *,
  .shopify-buy__quantity-container *,
  .shopify-buy__cart .shopify-buy__quantity-container * {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
  }

  .shopify-buy__cart .shopify-buy__cart-item__quantity span,
  .shopify-buy__cart .shopify-buy__cart-item__quantity div,
  .shopify-buy__quantity-container span,
  .shopify-buy__quantity-container div {
    color: #ffffff !important;
  }

  /* Pour les badges ou indicateurs de quantité */
  .shopify-buy__cart [class*="quantity"],
  .shopify-buy__cart [class*="qty"],
  .shopify-buy__cart [class*="count"] {
    color: #ffffff !important;
  }

  /* S'assurer que les labels de quantité sont visibles */
  .shopify-buy__cart label,
  .shopify-buy__cart .shopify-buy__cart-item__quantity label {
    color: rgba(255,255,255,0.9) !important;
  }

  /* Style pour les éléments de quantité dans les items du panier */
  .shopify-buy__cart-item .shopify-buy__cart-item__quantity,
  .shopify-buy__cart-item [class*="quantity"] {
    color: #ffffff !important;
  }

  /* Style générique pour tous les textes dans le conteneur de quantité */
  .shopify-buy__quantity-container,
  .shopify-buy__cart .shopify-buy__quantity-container {
    color: #ffffff !important;
  }

  .shopify-buy__quantity-container input,
  .shopify-buy__quantity-container button,
  .shopify-buy__quantity-container span,
  .shopify-buy__cart .shopify-buy__quantity-container input,
  .shopify-buy__cart .shopify-buy__quantity-container button,
  .shopify-buy__cart .shopify-buy__quantity-container span {
    color: #ffffff !important;
  }

  .shopify-buy__cart .shopify-buy__btn {
    background: linear-gradient(135deg, #ff2b2b, #d00000) !important;
    border-radius: 14px !important;
    font-weight: 800 !important;
    color: #fff !important;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    box-shadow: 0 16px 30px rgba(208,0,0,0.32);
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
  }

  .shopify-buy__cart .shopify-buy__btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 22px 44px rgba(255,43,43,0.38);
    filter: brightness(1.05);
  }

  .shopify-buy__cart .shopify-buy__cart__footer {
    background: #0b0b0d !important;
    border-top: 1px solid rgba(255,255,255,0.08);
  }

  /* ============================================
     RESPONSIVE DESIGN - MEDIA QUERIES
     ============================================ */

  /* Tablette et Desktop moyen (1024px et moins) */
  @media (max-width: 1024px) {
    .shop-grid {
      grid-template-columns: 1fr;
      gap: 40px;
    }
    
    .shop-size-grid {
      grid-template-columns: 1fr;
      gap: 30px;
    }
    
    .shop-mockup {
      min-height: 380px;
      padding: 30px;
    }
    
    .shop-mockup img {
      width: 84%;
    }
    
    .shop-product-card {
      padding: 25px;
    }
    
    .shop-price {
      font-size: 36px;
    }
    
    .shop-badges {
      margin: 25px 0 30px;
    }
    
    .shop-usp {
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
    }
    
    .shop-gallery {
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }
  }

  /* Tablette (768px et moins) */
  @media (max-width: 768px) {
    .shop-hero {
      padding: 40px 0;
    }
    
    .shop-grid {
      gap: 30px;
    }
    
    .shop-product-card {
      padding: 20px;
      border-radius: 20px;
    }
    
    .shop-mockup {
      min-height: 320px;
      padding: 25px;
      border-radius: 16px;
    }
    
    .shop-mockup-img {
      max-width: 360px;
    }
    
    .shop-price {
      font-size: 32px;
    }
    
    .shop-badges {
      gap: 10px;
      margin: 20px 0 25px;
    }
    
    .shop-badge {
      padding: 6px 12px;
      font-size: 11px;
    }
    
    .shop-product-tag {
      font-size: 10px;
      padding: 5px 10px;
    }
    
    .shop-usp {
      grid-template-columns: 1fr;
      gap: 14px;
    }
    
    .shop-usp-card {
      padding: 20px;
      border-radius: 16px;
    }
    
    .shop-usp-card h5 {
      font-size: 16px;
    }
    
    .shop-usp-card p {
      font-size: 13px;
    }
    
    .shop-tshirt-image-wrapper {
      border-radius: 20px;
    }
    
    .shop-tshirt-détails h4 {
      font-size: 22px;
    }
    
    .shop-tshirt-détails p {
      font-size: 13px;
    }
    
    .shop-tshirt-overlay {
      padding: 20px;
    }
    
    .shop-tshirt-label {
      font-size: 11px;
      padding: 6px 12px;
    }
    
    .shop-gallery {
      grid-template-columns: 1fr;
      gap: 16px;
    }
    
    .shop-size-table th,
    .shop-size-table td {
      padding: 12px 14px;
      font-size: 13px;
    }
    
    .shop-size-table th {
      font-size: 11px;
    }
    
    .shopify-buy-box {
      padding: 18px;
      border-radius: 16px;
    }
    
    .shopify-product-visual {
      min-height: 360px;
      padding: 16px;
      border-radius: 14px;
    }
    
    .shopify-visual-img {
      max-height: 300px;
    }

    /* Correction de l'écart avec le header sur tablette */
    #tt-content-wrap {
      padding-top: 80px !important;
    }
  }

  /* Mobile (640px et moins) */
  @media (max-width: 640px) {
    .shop-hero {
      padding: 30px 0;
    }
    
    .shop-grid {
      gap: 25px;
    }
    
    .shop-product-card {
      padding: 18px;
      border-radius: 18px;
    }
    
    .shop-mockup {
      min-height: 280px;
      padding: 20px;
      border-radius: 14px;
    }
    
    .shop-mockup-img {
      max-width: 300px;
    }
    
    .shop-controls {
      gap: 4px;
      padding: 4px;
    }
    
    .shop-control-btn {
      padding: 6px 14px;
      font-size: 11px;
    }
    
    .shop-price {
      font-size: 28px;
    }
    
    .shop-badges {
      gap: 8px;
      margin: 18px 0 20px;
    }
    
    .shop-badge {
      padding: 6px 12px;
      font-size: 10px;
    }
    
    .shop-cta-row {
      flex-direction: column;
      align-items: stretch;
      gap: 12px;
      margin-top: 20px;
    }
    
    .shop-stamp {
      padding: 8px 14px;
      font-size: 11px;
      justify-content: center;
    }
    
    .shop-usp {
      gap: 12px;
    }
    
    .shop-usp-card {
      padding: 18px;
      border-radius: 14px;
    }
    
    .shop-usp-card h5 {
      font-size: 15px;
      margin-bottom: 8px;
    }
    
    .shop-usp-card p {
      font-size: 13px;
      line-height: 1.5;
    }
    
    .shop-tshirt-image-wrapper {
      border-radius: 16px;
    }
    
    .shop-tshirt-détails {
      margin-top: 16px;
    }
    
    .shop-tshirt-détails h4 {
      font-size: 20px;
      margin-bottom: 6px;
    }
    
    .shop-tshirt-détails p {
      font-size: 12px;
    }
    
    .shop-tshirt-overlay {
      padding: 16px;
    }
    
    .shop-tshirt-label {
      font-size: 10px;
      padding: 5px 10px;
      border-radius: 6px;
    }
    
    .shop-gallery {
      gap: 14px;
    }
    
    .shop-gallery figure {
      border-radius: 16px;
    }
    
    .shop-gallery figcaption {
      padding: 14px;
      font-size: 13px;
    }
    
    .shop-size-table {
      border-radius: 12px;
    }
    
    .shop-size-table th,
    .shop-size-table td {
      padding: 10px 12px;
      font-size: 12px;
    }
    
    .shop-size-table th {
      font-size: 10px;
    }
    
    .shopify-buy-box {
      padding: 16px;
      border-radius: 14px;
    }
    
    .shopify-buy-box h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }
    
    .shopify-buy-box .shopify-buy__btn {
      height: 52px;
      font-size: 14px;
      border-radius: 12px !important;
    }
    
    .shopify-product-visual {
      min-height: 300px;
      padding: 14px;
      border-radius: 12px;
      margin: 14px 0 6px;
    }
    
    .shopify-visual-img {
      max-height: 260px;
    }
    
    .shopify-visual-controls {
      gap: 6px;
      margin-top: 12px;
      padding: 5px;
    }
    
    .shopify-visual-btn {
      padding: 6px 12px;
      font-size: 11px;
    }
    
    .flocage-fields {
      padding: 14px;
      border-radius: 10px;
      margin-top: 14px;
    }
    
    .flocage-fields label {
      font-size: 11px;
      margin-bottom: 5px;
    }
    
    .flocage-fields input {
      padding: 9px 11px;
      font-size: 13px;
      border-radius: 7px;
      margin-bottom: 7px;
    }
    
    .flocage-fields small {
      font-size: 9px;
      margin-bottom: 8px;
    }

    /* Correction de l'écart avec le header sur mobile */
    #tt-content-wrap {
      padding-top: 70px !important;
    }
  }

  /* Très petit mobile (480px et moins) */
  @media (max-width: 480px) {
    .shop-product-card {
      padding: 16px;
    }
    
    .shop-mockup {
      min-height: 240px;
      padding: 16px;
    }
    
    .shop-mockup-img {
      max-width: 260px;
    }
    
    .shop-price {
      font-size: 24px;
    }
    
    .shop-badges {
      gap: 6px;
      margin: 16px 0 18px;
    }
    
    .shop-badge {
      padding: 5px 10px;
      font-size: 9px;
    }
    
    .shop-product-tag {
      font-size: 9px;
      padding: 4px 8px;
    }
    
    .shop-usp-card {
      padding: 16px;
    }
    
    .shop-usp-card h5 {
      font-size: 14px;
    }
    
    .shop-usp-card p {
      font-size: 12px;
    }
    
    .shop-tshirt-détails h4 {
      font-size: 18px;
    }
    
    .shop-size-table th,
    .shop-size-table td {
      padding: 8px 10px;
      font-size: 11px;
    }
    
    .shop-size-table th {
      font-size: 9px;
    }
    
    .shopify-buy-box {
      padding: 14px;
    }
    
    .shopify-buy-box .shopify-buy__btn {
      height: 48px;
      font-size: 13px;
      margin-top: 20px;
    }
    
    .shopify-product-visual {
      min-height: 260px;
      padding: 12px;
    }
    
    .shopify-visual-img {
      max-height: 220px;
    }

    /* Correction de l'écart avec le header sur très petit mobile */
    #tt-content-wrap {
      padding-top: 60px !important;
    }
  }

  /* ============================================
     RESPONSIVE POUR LE GUIDE DES TAILLES
     ============================================ */

  /* Sur mobile : afficher les cards, masquer le tableau */
  @media (max-width: 768px) {
    .shop-size-cards {
      display: block;
    }

    .shop-size-table-desktop {
      display: none;
    }

    .shop-size-card {
      padding: 16px;
    }

    .shop-size-card-header h4 {
      font-size: 20px;
    }

    .shop-size-card-measures {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .shop-measure-value {
      font-size: 14px;
    }
  }

  /* Sur très petit mobile */
  @media (max-width: 480px) {
    .shop-size-card {
      padding: 14px;
      margin-bottom: 12px;
    }

    .shop-size-card-header {
      margin-bottom: 12px;
      padding-bottom: 8px;
    }

    .shop-size-card-header h4 {
      font-size: 18px;
    }

    .shop-measure-item {
      gap: 2px;
    }

    .shop-measure-label {
      font-size: 10px;
    }

    .shop-measure-value {
      font-size: 13px;
    }
  }

  /* Styles personnalisés pour le widget Shopify */
  .shopify-buy__product {
    background: transparent !important;
    min-height: 0;
    padding: 0 !important;
    box-shadow: none !important;
    border: none !important;
    position: relative;
  }

  .shopify-buy__product::before,
  .shopify-buy__product svg {
    display: none !important;
  }

  .shopify-buy__option-select__label {
    color: #fff !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    margin-bottom: 8px !important;
    display: block !important;
  }

  .shopify-buy__option-select {
    background: rgba(0,0,0,0.4) !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    border-radius: 8px !important;
    color: #fff !important;
    padding: 10px 12px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    width: 100% !important;
    cursor: pointer !important;
    transition: border-color 0.2s ease !important;
  }

  .shopify-buy__option-select:hover {
    border-color: rgba(255,255,255,0.25) !important;
  }

  .shopify-buy__option-select:focus {
    border-color: rgba(208,0,0,0.6) !important;
    outline: none !important;
  }

  .shopify-buy__option-select option {
    background: #1a1a1a !important;
    color: #fff !important;
  }

  /* On garde le sélecteur Shopify visible pour garantir le choix de taille */

  /* Conteneur des boutons de taille personnalisés */
  .custom-size-selector {
    margin-bottom: 20px;
  }

  .custom-size-label {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
    display: block;
  }

  .custom-size-buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
  }

  .custom-size-btn {
    background: rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px;
    color: rgba(255,255,255,0.7);
    padding: 12px 8px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
  }

  .custom-size-btn:hover {
    border-color: rgba(255,255,255,0.3);
    color: #fff;
    background: rgba(0,0,0,0.5);
  }

  .custom-size-btn.active {
    background: rgba(208,0,0,0.2);
    border-color: rgba(208,0,0,0.6);
    color: #fff;
    box-shadow: 0 0 12px rgba(208,0,0,0.3);
  }

  /* Boutons de taille personnalisés - Responsive */
  @media (max-width: 640px) {
    .custom-size-buttons {
      grid-template-columns: repeat(4, 1fr);
      gap: 6px;
    }
    
    .custom-size-btn {
      padding: 10px 6px;
      font-size: 11px;
    }
  }

  @media (max-width: 480px) {
    .custom-size-buttons {
      grid-template-columns: repeat(4, 1fr);
      gap: 5px;
    }
    
    .custom-size-btn {
      padding: 9px 5px;
      font-size: 10px;
      border-radius: 6px;
    }
    
    .custom-size-label {
      font-size: 11px;
      margin-bottom: 10px;
    }
  }

  @media (max-width: 380px) {
    .custom-size-buttons {
      grid-template-columns: repeat(4, 1fr);
      gap: 4px;
    }
    
    .custom-size-btn {
      padding: 8px 4px;
      font-size: 9px;
    }

    /* Correction de l'écart avec le header sur très petit écran */
    #tt-content-wrap {
      padding-top: 50px !important;
    }
  }

  @media (max-width: 768px) {
    .shopify-product-visual {
      min-height: 360px;
    }
  }

  /* Bouton PDF - Responsive */
  @media (max-width: 768px) {
    .shop-pdf-download .tt-btn {
      font-size: 13px;
      padding: 12px 20px;
    }

    .shop-pdf-download .tt-btn i {
      font-size: 14px;
    }

    .shop-pdf-download p {
      font-size: 11px;
    }
  }

  @media (max-width: 480px) {
    .shop-pdf-download .tt-btn {
      font-size: 12px;
      padding: 10px 16px;
    }

    .shop-pdf-download .tt-btn i {
      font-size: 13px;
    }
  }

  /* Visuel produit dédié au widget */
  .shopify-product-visual {
    position: relative;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: radial-gradient(circle at 30% 20%, rgba(208,0,0,0.14), transparent 35%), rgba(5,5,5,0.9);
    padding: 18px;
    margin: 16px 0 6px;
    min-height: 420px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
  }

  .shopify-product-visual-inner {
    position: relative;
    width: 100%;
    max-width: 440px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .shopify-visual-img {
    width: 100%;
    max-height: 360px;
    object-fit: contain;
    filter: drop-shadow(0 18px 28px rgba(0,0,0,0.35));
    opacity: 0;
    transform: scale(0.96);
    transition: opacity 0.25s ease, transform 0.25s ease;
    position: absolute;
  }

  .shopify-visual-img.active {
    opacity: 1;
    transform: scale(1);
    position: relative;
  }

  .shopify-visual-controls {
    display: inline-flex;
    gap: 8px;
    margin-top: 14px;
    background: rgba(255,255,255,0.04);
    padding: 6px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.08);
  }

  .shopify-visual-btn {
    border: none;
    padding: 8px 14px;
    border-radius: 999px;
    background: transparent;
    color: rgba(255,255,255,0.72);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .shopify-visual-btn:hover {
    color: #fff;
  }

  .shopify-visual-btn.active {
    background: linear-gradient(135deg, #ff2b2b, #d00000);
    color: #fff;
    box-shadow: 0 12px 25px rgba(208,0,0,0.32);
  }

  /* Champs flocage visibles en fallback */
  .flocage-fields {
    margin-top: 16px;
    padding: 16px;
    border-top: 1px solid rgba(255,255,255,0.08);
    background: rgba(0,0,0,0.25);
    border-radius: 12px;
  }

  .flocage-fields label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(255,255,255,0.9);
  }

  .flocage-fields input {
    width: 100%;
    padding: 10px 12px;
    background: rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
  }

  .flocage-fields small {
    display: block;
    margin-top: -4px;
    margin-bottom: 10px;
    font-size: 10px;
    color: rgba(255,255,255,0.55);
  }

  /* ============================================
     RESPONSIVE - SECTIONS ET ESPACEMENTS GÉNÉRAUX
     ============================================ */

  /* Tablette */
  @media (max-width: 768px) {
    .tt-section {
      padding-top: 50px !important;
      padding-bottom: 50px !important;
    }
    
    .tt-section.padding-top-xlg-60 {
      padding-top: 40px !important;
    }
    
    .tt-section.padding-bottom-xlg-60 {
      padding-bottom: 40px !important;
    }
    
    .tt-section.padding-bottom-xlg-100 {
      padding-bottom: 60px !important;
    }
    
    .tt-heading-xxxlg .tt-heading-title {
      font-size: 36px !important;
    }
    
    .tt-heading-xlg .tt-heading-title {
      font-size: 28px !important;
    }
    
    .tt-heading p {
      font-size: 14px !important;
    }
    
    .margin-top-60 {
      margin-top: 40px !important;
    }
    
    .margin-top-40 {
      margin-top: 30px !important;
    }
    
    .margin-bottom-40 {
      margin-bottom: 30px !important;
    }
    
    .margin-bottom-20 {
      margin-bottom: 16px !important;
    }
    
    .tt-row {
      margin-left: -10px;
      margin-right: -10px;
    }
    
    .tt-col-lg-6 {
      padding-left: 10px;
      padding-right: 10px;
    }
  }

  /* Mobile */
  @media (max-width: 640px) {
    .tt-section {
      padding-top: 40px !important;
      padding-bottom: 40px !important;
    }
    
    .tt-section.padding-top-xlg-60 {
      padding-top: 30px !important;
    }
    
    .tt-section.padding-bottom-xlg-60 {
      padding-bottom: 30px !important;
    }
    
    .tt-section.padding-bottom-xlg-100 {
      padding-bottom: 50px !important;
    }
    
    .tt-section.no-padding {
      padding-top: 0 !important;
      padding-bottom: 0 !important;
    }
    
    .tt-section.padding-top-xlg-20 {
      padding-top: 15px !important;
    }
    
    .tt-section.padding-bottom-xlg-20 {
      padding-bottom: 15px !important;
    }
    
    .tt-heading-xxxlg .tt-heading-title {
      font-size: 28px !important;
      line-height: 1.2 !important;
    }
    
    .tt-heading-xlg .tt-heading-title {
      font-size: 24px !important;
      line-height: 1.2 !important;
    }
    
    .tt-heading p {
      font-size: 13px !important;
      line-height: 1.5 !important;
    }
    
    .max-width-600 {
      max-width: 100% !important;
      padding: 0 10px;
    }
    
    .max-width-900 {
      max-width: 100% !important;
    }
    
    .margin-top-60 {
      margin-top: 30px !important;
    }
    
    .margin-top-40 {
      margin-top: 25px !important;
    }
    
    .margin-top-20 {
      margin-top: 16px !important;
    }
    
    .margin-bottom-40 {
      margin-bottom: 25px !important;
    }
    
    .margin-bottom-20 {
      margin-bottom: 14px !important;
    }
    
    .tt-row {
      margin-left: -8px;
      margin-right: -8px;
    }
    
    .tt-col-lg-6 {
      padding-left: 8px;
      padding-right: 8px;
      margin-bottom: 20px;
    }
    
    .tt-wrap {
      padding-left: 16px;
      padding-right: 16px;
    }
    
    .tt-btn {
      padding: 12px 24px !important;
      font-size: 13px !important;
    }
    
    .tt-accordion-item {
      margin-bottom: 12px;
    }
    
    .tt-ac-head-title {
      font-size: 15px !important;
    }
    
    .tt-accordion-content p {
      font-size: 13px !important;
      line-height: 1.6 !important;
    }
  }

  /* Très petit mobile */
  @media (max-width: 480px) {
    .tt-section {
      padding-top: 30px !important;
      padding-bottom: 30px !important;
    }
    
    .tt-section.padding-top-xlg-60 {
      padding-top: 25px !important;
    }
    
    .tt-section.padding-bottom-xlg-60 {
      padding-bottom: 25px !important;
    }
    
    .tt-section.padding-bottom-xlg-100 {
      padding-bottom: 40px !important;
    }
    
    .tt-heading-xxxlg .tt-heading-title {
      font-size: 24px !important;
      line-height: 1.15 !important;
    }
    
    .tt-heading-xlg .tt-heading-title {
      font-size: 20px !important;
      line-height: 1.2 !important;
    }
    
    .tt-heading p {
      font-size: 12px !important;
    }
    
    .margin-top-60 {
      margin-top: 25px !important;
    }
    
    .margin-top-40 {
      margin-top: 20px !important;
    }
    
    .margin-top-20 {
      margin-top: 14px !important;
    }
    
    .margin-bottom-40 {
      margin-bottom: 20px !important;
    }
    
    .margin-bottom-20 {
      margin-bottom: 12px !important;
    }
    
    .tt-wrap {
      padding-left: 12px;
      padding-right: 12px;
    }
    
    .tt-btn {
      padding: 10px 20px !important;
      font-size: 12px !important;
    }
    
    .tt-ac-head-title {
      font-size: 14px !important;
    }
    
    .tt-accordion-content p {
      font-size: 12px !important;
    }
  }

</style>

@endverbatim
@endsection


@section('content')
@verbatim

<div class="tt-section shop-hero padding-top-xlg-140 padding-bottom-xlg-120">
        <div class="tt-section-inner tt-wrap shop-grid">
          <div>
            <div class="tt-heading tt-heading-xxxlg">
              <h1 class="tt-heading-title tt-text-reveal">Boutique<br>ERAH Esport</h1>
            </div>
            <p class="text-lg font-500 tt-text-reveal" style="max-width: 640px; color: rgba(255,255,255,0.8);">
              Le T-shirt officiel floqué, pensé pour le grind comme pour la vie de tous les jours.
              Matière premium, coupe unisexe, flocage personnalisé par Intersport.
            </p>

            <div class="shop-badges">
              <div class="shop-badge"><i class="fas fa-check" style="color: var(--shop-accent); margin-right: 6px;"></i> Flocage intégré</div>
              <div class="shop-badge">Du XS au 4XL</div>
              <div class="shop-badge">Expédition suivie</div>
              <div class="shop-badge">Stock illimité</div>
            </div>

            <div class="shop-cta-row">
              <a href="#acheter" class="tt-btn tt-btn-primary tt-magnetic-item">
                <span data-hover="Acheter maintenant">Acheter maintenant</span>
              </a>
              <a href="#détails" class="tt-btn tt-btn-outline tt-magnetic-item">
                <span data-hover="Voir les détails">Voir les détails</span>
              </a>
              <div class="shop-stamp">
                <i class="fa-solid fa-shield-halved"></i>
                Paiement sécurisé
              </div>
            </div>
          </div>

          <div class="shop-product-card tt-anim-fadeinup">
            <div class="shop-product-top">
              <div class="shop-product-tag">DROP OFFICIEL 2026</div>
              <div class="shop-price">48,00 €</div>
            </div>
            <div class="shop-mockup tt-tilt-effect">
              <div class="shop-mockup-images tt-tilt-content">
                <img src="/template/assets/img/boutique/tshirt-face-avant.png" alt="T-shirt ERAH Esport face avant" class="shop-mockup-img active" id="img-face">
                <img src="/template/assets/img/boutique/tshirt-dos.png" alt="T-shirt ERAH Esport dos" class="shop-mockup-img" id="img-dos">
              </div>
              
              <div class="shop-controls">
                <button class="shop-control-btn active" onclick="switchView('face')" id="btn-face">Face</button>
                <button class="shop-control-btn" onclick="switchView('dos')" id="btn-dos">Dos</button>
              </div>
            </div>
            <div class="shop-cta-row" style="justify-content: space-between; margin-top: 0;">
                <div class="text-sm tt-text-uppercase" style="font-weight: 600; letter-spacing: 0.05em;">Flocage inclus dans le prix</div>
              <a href="#faq" class="tt-link">FAQ commande</a>
            </div>
          </div>
        </div>
      </div>


      <div class="tt-section padding-top-xlg-80 padding-bottom-xlg-40" id="acheter">
        <div class="tt-section-inner tt-wrap">
          <div class="tt-heading tt-heading-xxxlg tt-heading-center">
            <h2 class="tt-heading-title tt-text-reveal">Acheter maintenant</h2>
            <p class="max-width-500 tt-text-uppercase tt-text-reveal">Checkout Shopify intégré, sans quitter le site.</p>
          </div>

          <div class="tt-row margin-top-40">
            <div class="tt-col-lg-6">
              <div class="shopify-buy-box">
                <h3 class="tt-heading-title">T-shirt floqué ERAH</h3>
                <div class="shopify-price-row">
                  <div class="shopify-price">48,00&nbsp;€</div>
                  <div class="text-xs tt-text-uppercase" style="color:rgba(255,255,255,0.65);">Tarif officiel</div>
                </div>
                <div class="shopify-product-visual">
                  <div class="shopify-product-visual-inner">
                    <img src="/template/assets/img/boutique/tshirt-face-avant.png" alt="T-shirt ERAH - face" class="shopify-visual-img active" id="buy-visual-face">
                    <img src="/template/assets/img/boutique/tshirt-dos.png" alt="T-shirt ERAH - dos" class="shopify-visual-img" id="buy-visual-dos">
                  </div>
                  <div class="shopify-visual-controls">
                    <button class="shopify-visual-btn active" data-view="face">Face</button>
                    <button class="shopify-visual-btn" data-view="dos">Dos</button>
                  </div>
                </div>
                <p class="text-sm">Choisis ta taille, ajoute ton flocage, et paie en checkout sécurisé Shopify sans quitter la page.</p>
                <p class="text-xs tt-text-uppercase" style="color: rgba(255,255,255,0.78); margin-bottom: 12px;">
                  Dans le panier, mets ton <strong>pseudo (max 6 caractères)</strong> et ton <strong>numéro (max 2)</strong> dans la case <em>Special instructions for seller</em>.
                </p>
                
                <div id="shopify-buy-button"></div>
                <p class="text-xs tt-text-uppercase" style="margin-top:12px; opacity: 0.6;">Paiement sécurisé · Livraison suivie · Expédition 2/3 semaines</p>
              </div>
            </div>
            <div class="tt-col-lg-6">
  <div class="shop-usp">

    <div class="shop-usp-card">
      <h5>
        <i class="fab fa-shopify" style="color: #96bf48; margin-right: 8px;"></i>
        Checkout Shopify
      </h5>
      <p>
        Paiement sécurisé et conforme via Shopify.
        Vos informations bancaires sont protégées selon les standards de sécurité en vigueur.
      </p>
    </div>

    <div class="shop-usp-card">
      <h5>
        <i class="fas fa-truck" style="color: #4ade80; margin-right: 8px;"></i>
        Livraison & Retrait
      </h5>
      <p>
        Maillots officiels <strong>déjà disponibles</strong> en partenariat avec Intersport.
        <br>
        <strong>Délais :</strong>
        <br>
        • <strong>2 semaines</strong> pour un retrait local à <strong>Mende</strong>
        (chez Intersport Mende)
        <br>
        • <strong>3 semaines</strong> pour une livraison à domicile partout en France
        via <strong>Chronopost</strong>
      </p>
    </div>

    <div class="shop-usp-card">
      <h5>
        <i class="fas fa-pen-fancy" style="color: var(--shop-accent); margin-right: 8px;"></i>
        Flocage intégré
      </h5>
      <p>
        Personnalisez votre maillot avec votre pseudo et un numéro.
        Flocage durable réalisé avec des finitions professionnelles,
        fidèle aux couleurs officielles ERAH.
      </p>
    </div>

    <div class="shop-usp-card">
      <h5>
        <i class="fas fa-shirt" style="color: var(--shop-accent); margin-right: 8px;"></i>
        Coupe & confort
      </h5>
      <p>
        Textile technique <strong>100% polyester</strong>, respirant et résistant.
        Coupe unisexe pensée pour le confort, en compétition comme au quotidien.
        <br>
        <strong>Conseil taille :</strong> le maillot taille légèrement petit,
        nous recommandons de prendre une taille au-dessus.
      </p>
    </div>

    <div class="shop-usp-card">
      <h5>
        <i class="fas fa-store" style="color: var(--shop-accent); margin-right: 8px;"></i>
        Partenariat Intersport
      </h5>
      <p>
        Maillot officiel ERAH conçu et distribué en partenariat avec
        <strong>Intersport Mende</strong>.
        Qualité premium et finitions professionnelles garanties.
      </p>
    </div>

    <div class="shop-usp-card">
      <h5>
        <i class="fas fa-headset" style="margin-right: 8px;"></i>
        Support humain
      </h5>
      <p>
        Une question avant ou après votre commande ?
        <br>
        Contactez-nous :
        <a href="mailto:erah.association@gmail.com" class="tt-link">
          erah.association@gmail.com
        </a>
      </p>
    </div>

  </div>
</div>


          </div>
        </div>
      </div>


      <div class="tt-section" id="détails">
        <div class="tt-section-inner tt-wrap">
          <div class="tt-heading tt-heading-xxxlg tt-heading-center">
            <h2 class="tt-heading-title tt-text-reveal">T-shirt floqué ERAH</h2>
            <p class="max-width-600 tt-text-uppercase tt-text-reveal">Une seule pièce, toute l'identité ERAH.</p>
          </div>

          <div class="tt-row margin-top-60">
            <div class="tt-col-lg-6">
              <div class="shop-usp-card margin-bottom-20">
                <h5>Coupe & confort</h5>
                <p>Maille douce 100% coton, épaules renforcées pour rester nette en LAN comme au quotidien.</p>
              </div>
              <div class="shop-usp-card margin-bottom-20">
                <h5>Flocage personnalisé</h5>
                <p>Ajoutez votre pseudo et un numéro. Impression résistante, couleurs fidèles à notre rouge emblématique.</p>
              </div>
              <div class="shop-usp-card">
                <h5>Finition premium</h5>
                <p>Etiquette tissée ERAH, surpiqûres propres, bande de propreté et traitement anti-boulochage.</p>
              </div>
            </div>
            <div class="tt-col-lg-6">
              <div class="shop-usp-card" style="height: 100%; display: flex; flex-direction: column; justify-content: center; background: linear-gradient(145deg, rgba(208,0,0,0.1), transparent);">
                <h5>Pourquoi ce drop ?</h5>
                <p>Une pièce unique pour soutenir l’équipe et afficher fièrement vos couleurs, pensée comme un symbole fort plutôt qu’une simple collection.</p>
                <div class="shop-cta-row" style="margin-top: 20px;">
                  <a href="#acheter" class="tt-btn tt-btn-secondary tt-magnetic-item">
                    <span data-hover="Je floque le mien">Je floque le mien</span>
                  </a>
                  <div class="shop-stamp">
                    <i class="fa-regular fa-clock"></i>
                    Expédition 30 jours
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


<div class="tt-section padding-top-xlg-60 padding-bottom-xlg-60">
  <div class="tt-section-inner tt-wrap shop-size-grid">
    <div>
      <div class="tt-heading tt-heading-xlg">
        <h3 class="tt-heading-title tt-text-reveal">Guide des tailles</h3>
      </div>
      <p class="text-sm">Coupe unisexe. Si vous hésitez entre deux tailles, prenez la taille au-dessus pour garder l'effet street/oversize.</p>

      <!-- Version mobile-friendly : Cards pour chaque taille -->
      <div class="shop-size-cards margin-top-20">
        <div class="shop-size-card" data-size="XS">
          <div class="shop-size-card-header">
            <h4>XS</h4>
            <span class="shop-size-badge">Petite taille</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">67 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">34 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">68 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">31 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">65 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">46 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="S">
          <div class="shop-size-card-header">
            <h4>S</h4>
            <span class="shop-size-badge">Taille standard</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">69 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">36 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">70 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">33 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">67 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">46 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="M">
          <div class="shop-size-card-header">
            <h4>M</h4>
            <span class="shop-size-badge">Taille moyenne</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">71 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">38 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">73 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">35 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">69 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">47 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="L">
          <div class="shop-size-card-header">
            <h4>L</h4>
            <span class="shop-size-badge">Grande taille</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">74 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">39 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">74 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">37 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">71 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">49 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="XL">
          <div class="shop-size-card-header">
            <h4>XL</h4>
            <span class="shop-size-badge">Très grande taille</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">76 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">41 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">76 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">38 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">73 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">50 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="2XL">
          <div class="shop-size-card-header">
            <h4>2XL</h4>
            <span class="shop-size-badge">2XL</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">77 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">43 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">78 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">39 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">75 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">50 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="3XL">
          <div class="shop-size-card-header">
            <h4>3XL</h4>
            <span class="shop-size-badge">3XL</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">78 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">45 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">81 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">41 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">77 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">50 cm</span>
            </div>
          </div>
        </div>

        <div class="shop-size-card" data-size="4XL">
          <div class="shop-size-card-header">
            <h4>4XL</h4>
            <span class="shop-size-badge">4XL</span>
          </div>
          <div class="shop-size-card-measures">
            <div class="shop-measure-item">
              <span class="shop-measure-label">Loire</span>
              <span class="shop-measure-value">80 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Seine</span>
              <span class="shop-measure-value">47 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Rhone</span>
              <span class="shop-measure-value">83 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Garonne</span>
              <span class="shop-measure-value">43 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Meuse</span>
              <span class="shop-measure-value">81 cm</span>
            </div>
            <div class="shop-measure-item">
              <span class="shop-measure-label">Dordogne</span>
              <span class="shop-measure-value">50 cm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Version desktop : Tableau traditionnel (masqué sur mobile) -->
      <div class="shop-size-table-desktop">
        <table class="shop-size-table margin-top-20">
          <thead>
            <tr>
              <th>Taille</th>
              <th>Loire (cm)</th>
              <th>Seine (cm)</th>
              <th>Rhone (cm)</th>
              <th>Garonne (cm)</th>
              <th>Meuse (cm)</th>
              <th>Dordogne (cm)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>XS</td>
              <td>67</td>
              <td>34</td>
              <td>68</td>
              <td>31</td>
              <td>65</td>
              <td>46</td>
            </tr>
            <tr>
              <td>S</td>
              <td>69</td>
              <td>36</td>
              <td>70</td>
              <td>33</td>
              <td>67</td>
              <td>46</td>
            </tr>
            <tr>
              <td>M</td>
              <td>71</td>
              <td>38</td>
              <td>73</td>
              <td>35</td>
              <td>69</td>
              <td>47</td>
            </tr>
            <tr>
              <td>L</td>
              <td>74</td>
              <td>39</td>
              <td>74</td>
              <td>37</td>
              <td>71</td>
              <td>49</td>
            </tr>
            <tr>
              <td>XL</td>
              <td>76</td>
              <td>41</td>
              <td>76</td>
              <td>38</td>
              <td>73</td>
              <td>50</td>
            </tr>
            <tr>
              <td>2XL</td>
              <td>77</td>
              <td>43</td>
              <td>78</td>
              <td>39</td>
              <td>75</td>
              <td>50</td>
            </tr>
            <tr>
              <td>3XL</td>
              <td>78</td>
              <td>45</td>
              <td>81</td>
              <td>41</td>
              <td>77</td>
              <td>50</td>
            </tr>
            <tr>
              <td>4XL</td>
              <td>80</td>
              <td>47</td>
              <td>83</td>
              <td>43</td>
              <td>81</td>
              <td>50</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="shop-usp">
      <div class="shop-usp-card">
        <h5>Conseil fit</h5>
        <p>Le jersey garde de la tenue mais reste souple. Pour un look plus ajusté, choisissez votre taille habituelle.</p>
      </div>
      <div class="shop-usp-card">
        <h5>Entretien</h5>
        <p>Lavage à 30°C sur l'envers, pas de sèche-linge pour préserver le flocage.</p>
        <br>
        <p>Ne pas laver le maillot avec des vêtements comportant des scratchs.</p>
      </div>
      <div class="shop-usp-card">
        <h5>Support</h5>
        <p>Une question sur la commande ? Contactez-nous sur <a href="mailto:erah.association@gmail.com" class="tt-link">erah.association@gmail.com</a>.</p>
      </div>
    </div>
  </div>
</div>

<div class="shop-pdf-download margin-bottom-30">
        <a href="/template/assets/docs/Catalogue-Maillot-Francais X Erah.pdf" target="_blank" class="tt-btn tt-btn-outline tt-magnetic-item">
          <i style="margin-right: 8px;"></i>
          <span data-hover="Télécharger le guide complet">Télécharger le guide complet</span>
        </a>
        <p class="text-xs" style="margin-top: 8px; color: rgba(255,255,255,0.6);">
          Toutes les informations techniques sur le maillot et le textile
        </p>
      </div>





      <div class="tt-section no-padding padding-top-xlg-20 padding-bottom-xlg-20">
        <div class="tt-section-inner">
          <div class="tt-scrolling-text-crossed">
            <div class="tt-scrolling-text-crossed-inner">
              <div class="tt-scrolling-text scrt-dyn-separator scrt-color-reverse" data-scroll-speed="7" data-change-direction="true" data-opposite-direction="true">
                <div class="tt-scrt-inner">
                  <div class="tt-scrt-content">
                    Paiement sécurisé · Livraison suivie · Flocage personnalisé · Support humain
                    <span class="tt-scrt-separator">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                      </svg>
                    </span>
                  </div>
                </div>
              </div>
              <div class="tt-scrolling-text scrt-dyn-separator" data-scroll-speed="7" data-change-direction="true">
                <div class="tt-scrt-inner">
                  <div class="tt-scrt-content">
                    Paiement sécurisé · Livraison suivie · Flocage personnalisé · Support humain
                    <span class="tt-scrt-separator">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                      </svg>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Section Galerie T-shirt -->
      <div class="tt-section padding-top-xlg-60 padding-bottom-xlg-60" id="galerie-tshirt">
        <div class="tt-section-inner tt-wrap">
          <div class="tt-heading tt-heading-xxxlg tt-heading-center">
            <h2 class="tt-heading-title tt-text-reveal">Le T-shirt en détail</h2>
            <p class="max-width-600 tt-text-uppercase tt-text-reveal">Design exclusif, flocage personnalisé, qualité premium.</p>
          </div>
          
          <div class="tt-row margin-top-60">
            <div class="tt-col-lg-6 margin-bottom-40">
              <div class="shop-tshirt-showcase">
                <div class="shop-tshirt-image-wrapper tt-tilt-effect">
                  <div class="tt-tilt-content" style="width:100%; height:100%;">
                    <img src="/template/assets/img/boutique/tshirt-face-avant.png" alt="T-shirt ERAH face avant - Logo ERAH rouge au centre avec logos partenaires" class="shop-tshirt-image" loading="lazy">
                    <div class="shop-tshirt-overlay">
                      <div class="shop-tshirt-label">Face avant</div>
                    </div>
                  </div>
                </div>
                <div class="shop-tshirt-détails">
                  <h4>Face avant</h4>
                  <p class="text-sm">Design noir structuré, parcouru de lignes abstraites rouges, intégrant les partenaires et l’identité ERAH avec sobriété.</p>
                </div>
              </div>
            </div>
            <div class="tt-col-lg-6 margin-bottom-40">
              <div class="shop-tshirt-showcase">
                <div class="shop-tshirt-image-wrapper tt-tilt-effect">
                  <div class="tt-tilt-content" style="width:100%; height:100%;">
                    <img src="/template/assets/img/boutique/tshirt-dos.png" alt="T-shirt ERAH dos - Flocage personnalisé avec pseudo et numéro" class="shop-tshirt-image" loading="lazy">
                    <div class="shop-tshirt-overlay">
                      <div class="shop-tshirt-label">Dos personnalisable</div>
                    </div>
                  </div>
                </div>
                <div class="shop-tshirt-détails">
                  <h4>Dos avec flocage</h4>
                  <p class="text-sm">Personnalisation du pseudo et du numéro, pour une lisibilité forte et une identité assumée.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tt-section padding-top-xlg-180">
						<div class="tt-section-inner">
							<div class="tt-clipper">
								<a href="https://youtu.be/hjwDfV0gR6U" class="tt-clipper-inner" data-cursor="Play" data-fancybox data-caption="Découvrez le Teaser de notre maillot ERAH">

									<div class="tt-clipper-bg">
										<video loop muted autoplay playsinline preload="metadata" poster="/template/assets/img/logo-fond.png">
											<source src="/template/assets/vids/erah-2026-boutique.mp4" data-src="/template/assets/vids/erah-2026-boutique.mp4" type="video/mp4">
											<source src="/template/assets/vids/erah-2026-boutique.webm" data-src="/template/assets/vids/erah-2026-boutique.webm" type="video/webm">
										</video>
									</div>

									<div class="tt-clipper-content">

										<div class="tt-clipper-btn">
											<i class="fa-solid fa-play"></i>
										</div>

									</div>
								</a> 
							</div>

						</div>
					</div>

          <div class="tt-section">
						<div class="tt-section-inner">

							<div class="tt-sticky-horizontal-scroll" data-speed="2000" data-direction="left">
								<div class="tt-shs-pin-wrap">
									<div class="tt-shs-animation-wrap">
										<div class="tt-shs-item">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/boutique/photo-1.jpg" class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Détails du textile
													</figcaption>
												</figure>
											</div>
										</div>
										
										<div class="tt-shs-item">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/boutique/photo-2.jpg" class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Détails logos et flocage
													</figcaption>
												</figure>
											</div> 
										</div>
										
										<div class="tt-shs-item">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/boutique/photo-3.jpg" class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Détails du textile et des finitions
													</figcaption>
												</figure>
											</div>
										</div>
										
										<div class="tt-shs-item">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/boutique/photo-4.jpg" class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Founrisseurs et partenaires
													</figcaption>
												</figure>
											</div> 
										</div>

									</div> 

									<div class="tt-shs-keep-scrolling">
										<span>Keep scrolling</span>
										<i class="fa-solid fa-arrows-up-down"></i>
									</div>

								</div> 
							</div>

						</div>
					</div>

      <div class="tt-section">
        <div class="tt-section-inner tt-wrap">
          <div class="tt-heading tt-heading-xxxlg tt-heading-center">
            <h2 class="tt-heading-title tt-text-reveal">Pensé pour la scène</h2>
            <p class="max-width-600 tt-text-uppercase tt-text-reveal">Le même ADN que nos joueurs en LAN.</p>
          </div>
          <div class="shop-gallery margin-top-40">
            <figure>
              <img src="/template/assets/img/galerie/Hoplan-2025-2.webp" loading="lazy" alt="Equipe ERAH en LAN">
              <figcaption>HOPLAN 2025</figcaption>
            </figure>
            <figure>
              <img src="/template/assets/img/galerie/gamers-assembly-2025-1.webp" loading="lazy" alt="ERAH Gamers Assembly">
              <figcaption>Gamers Assembly</figcaption>
            </figure>
            <figure>
              <img src="/template/assets/img/galerie/Hoplan-2025-1.webp" loading="lazy" alt="Backstage ERAH">
              <figcaption>Backstage roster VCL</figcaption>
            </figure>
            <figure>
              <img src="/template/assets/img/galerie/interview-HopLan-2025.webp" loading="lazy" alt="Interview ERAH">
              <figcaption>Interview & contenu</figcaption>
            </figure>
          </div>
        </div>
      </div>


      <div class="tt-section padding-bottom-xlg-100" id="faq">
        <div class="tt-section-inner tt-wrap">
          <div class="tt-heading tt-heading-xlg tt-heading-center">
            <h3 class="tt-heading-title tt-text-reveal">FAQ rapide</h3>
          </div>
          <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter margin-top-40">
            <div class="tt-accordion-item tt-anim-fadeinup">
              <div class="tt-accordion-heading">
                <div class="tt-ac-head cursor-alter">
                  <div class="tt-ac-head-inner">
                    <h4 class="tt-ac-head-title">Où se passe le flocage ?</h4>
                  </div>
                </div>
                <div class="tt-accordion-caret">
                  <div class="tt-accordion-caret-inner tt-magnetic-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                      <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="tt-accordion-content max-width-900">
                <p>Directement via Shopify : ajoutez votre pseudo/numéro avant de valider. Chaque pièce est contrôlée avant expédition.</p>
              </div>
            </div>

            <div class="tt-accordion-item tt-anim-fadeinup">
              <div class="tt-accordion-heading">
                <div class="tt-ac-head cursor-alter">
                  <div class="tt-ac-head-inner">
                    <h4 class="tt-ac-head-title">Quels sont les délais de livraison ?</h4>
                  </div>
                </div>
                <div class="tt-accordion-caret">
                  <div class="tt-accordion-caret-inner tt-magnetic-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                      <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="tt-accordion-content max-width-900">
                <p>2 semaines si vous habitez à <strong>Mende</strong> (retrait chez <strong>Intersport Mende</strong>). Maximum 3 semaines en dehors de Mende. Toutes les 2 semaines, nous faisons un check-up pour expédier les commandes.</p>
              </div>
            </div>

            <div class="tt-accordion-item tt-anim-fadeinup">
              <div class="tt-accordion-heading">
                <div class="tt-ac-head cursor-alter">
                  <div class="tt-ac-head-inner">
                    <h4 class="tt-ac-head-title">C'est quoi le partenariat Intersport ?</h4>
                  </div>
                </div>
                <div class="tt-accordion-caret">
                  <div class="tt-accordion-caret-inner tt-magnetic-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                      <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="tt-accordion-content max-width-900">
                <p>Les t-shirts ERAH sont des <strong>produits Intersport</strong> de qualité premium, floqués avec notre design exclusif. Ce partenariat garantit une qualité textile professionnelle et un confort optimal.</p>
              </div>
            </div>

            <div class="tt-accordion-item tt-anim-fadeinup">
              <div class="tt-accordion-heading">
                <div class="tt-ac-head cursor-alter">
                  <div class="tt-ac-head-inner">
                    <h4 class="tt-ac-head-title">Peut-on retourner ou échanger ?</h4>
                  </div>
                </div>
                <div class="tt-accordion-caret">
                  <div class="tt-accordion-caret-inner tt-magnetic-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                      <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="tt-accordion-content max-width-900">
                <p>Oui pour les pièces non personnalisées sous 14 jours. Pour les produits floqués, nous gérons les échanges en cas de défaut ou problème de taille (contactez-nous avant retour).</p>
              </div>
            </div>
          </div>
        </div>
      </div>


@endverbatim
@endsection


@section('page_scripts')
@verbatim

<script src="/template/assets/vendor/jquery/jquery.min.js" defer></script>
<script src="/template/assets/vendor/gsap/gsap.min.js" defer></script>
<script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js" defer></script>
<script src="/template/assets/vendor/gsap/ScrollTrigger.min.js" defer></script>
<script src="/template/assets/vendor/lenis.min.js" defer></script>
<script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/isotope/isotope.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/fancybox/js/fancybox.umd.js" defer></script>
<script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js" defer></script>
<script src="/template/assets/js/theme.js" defer></script>
<script src="/template/assets/js/cookies.js" defer></script>

@endverbatim
@endsection
