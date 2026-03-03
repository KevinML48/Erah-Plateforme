(function () {
  // 1. Injecter le modal de checkout dans le DOM s'il n'existe pas
  if (!document.getElementById("shopify-checkout-modal")) {
    var modalHTML = `
      <div id="shopify-checkout-modal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.9); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
        <div style="position: relative; width: 100%; max-width: 1200px; height: 90vh; max-height: 900px; background: #0b0b0d; border-radius: 18px; box-shadow: 0 24px 60px rgba(0,0,0,0.8); border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
          <button id="close-checkout-modal" style="position: absolute; top: 20px; right: 20px; z-index: 100000; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 300; transition: all 0.2s ease; line-height: 1;" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.borderColor='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(255,255,255,0.2)'">
            ×
          </button>
          <iframe id="shopify-checkout-iframe" src="" style="width: 100%; height: 100%; border: none; display: block; background: #fff;"></iframe>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);
  }

  // Configuration
  var shopifyDomain = "erah-shop-2.myshopify.com";
  var storefrontAccessToken = "dc06e819fed83c122ae8459a50eef09a";
  var productId = "10496961577287";
  var buyButtonTarget = document.getElementById("shopify-buy-button"); // Peut être null sur d'autres pages

  var checkoutModal = document.getElementById("shopify-checkout-modal");
  var checkoutIframe = document.getElementById("shopify-checkout-iframe");
  var closeModalBtn = document.getElementById("close-checkout-modal");
  var shopifyClient = null;
  var cartComponent = null;

  // Fermer le modal
  function closeCheckoutModal() {
    checkoutModal.style.display = "none";
    checkoutIframe.src = "";
    document.body.style.overflow = "";
  }

  // Ouvrir le modal avec l'URL du checkout
  function openCheckoutModal(checkoutUrl) {
    if (checkoutUrl) {
      checkoutModal.style.display = "flex";
      checkoutIframe.src = checkoutUrl;
      document.body.style.overflow = "hidden";

      // Fermer automatiquement après paiement réussi (détection via URL de l'iframe)
      var checkInterval = setInterval(function () {
        try {
          var iframeUrl = checkoutIframe.contentWindow.location.href;
          if (
            iframeUrl &&
            (iframeUrl.indexOf("/thank_you") !== -1 ||
              iframeUrl.indexOf("/orders/") !== -1)
          ) {
            clearInterval(checkInterval);
            setTimeout(function () {
              closeCheckoutModal();
              alert("Commande confirmée ! Merci pour votre achat.");
            }, 2000);
          }
        } catch (e) {
          // Cross-origin, on ne peut pas lire l'URL
        }
      }, 500);

      // Nettoyer l'interval après 10 minutes
      setTimeout(function () {
        clearInterval(checkInterval);
      }, 600000);
    }
  }

  if (closeModalBtn)
    closeModalBtn.addEventListener("click", closeCheckoutModal);
  if (checkoutModal) {
    checkoutModal.addEventListener("click", function (e) {
      if (e.target === checkoutModal) {
        closeCheckoutModal();
      }
    });
  }

  // Écouter les messages du checkout pour fermer automatiquement après paiement
  window.addEventListener("message", function (event) {
    if (
      event.data &&
      (event.data.type === "shopify:checkout:close" ||
        event.data === "closeCheckout")
    ) {
      closeCheckoutModal();
    }
  });

  // Intercepter tous les clics sur les liens/a qui pointent vers checkout.shopify.com
  document.addEventListener(
    "click",
    function (e) {
      var target = e.target;
      while (target && target.tagName !== "A" && target.tagName !== "BUTTON") {
        target = target.parentElement;
      }

      if (target) {
        var href = target.href || target.getAttribute("href");
        var isCheckoutLink =
          href &&
          (href.indexOf("checkout.shopify.com") !== -1 ||
            href.indexOf("/checkouts/") !== -1);

        if (isCheckoutLink) {
          e.preventDefault();
          e.stopPropagation();
          openCheckoutModal(href);
          return false;
        }
      }
    },
    true
  );

  // Reset panier persistant pour éviter d'anciens quantités > 1
  try {
    Object.keys(localStorage || {}).forEach(function (key) {
      if (key.indexOf("shopify-buy") === 0) {
        localStorage.removeItem(key);
      }
    });
  } catch (e) {}

  function loadScript() {
    var script = document.createElement("script");
    script.async = true;
    script.src =
      "https://sdks.shopifycdn.com/buy-button/latest/buy-button-storefront.min.js";
    script.onload = shopifyBuyInit;
    document.head.appendChild(script);
  }

  function shopifyBuyInit() {
    shopifyClient = ShopifyBuy.buildClient({
      domain: shopifyDomain,
      storefrontAccessToken: storefrontAccessToken,
    });

    ShopifyBuy.UI.onReady(shopifyClient).then(function (ui) {
      // Configuration commune du panier
      var cartConfig = {
        popup: false,
        startOpen: false,
        contents: {
          note: false,
        },
        text: {
          title: "Panier ERAH",
          empty: "Votre panier est vide",
          total: "Sous-total",
          button: "Passer la commande",
        },
        styles: {
          cart: {
            "background-color": "#0b0b0d",
            color: "#ffffff",
            "max-width": "420px",
            width: "100%",
            "box-shadow": "-12px 0 40px rgba(0,0,0,0.55)",
            border: "1px solid rgba(255,255,255,0.08)",
            "border-left": "none",
            "border-radius": "0 18px 18px 0",
            "backdrop-filter": "blur(8px)",
          },
          footer: {
            "background-color": "#0b0b0d",
            "border-top": "1px solid rgba(255,255,255,0.08)",
            padding: "18px",
          },
          title: {
            color: "#ffffff",
            "text-transform": "uppercase",
            "letter-spacing": "0.06em",
            "font-weight": "800",
            "font-size": "14px",
            padding: "18px",
          },
          lineItems: {
            color: "#ffffff",
          },
          price: {
            color: "#ffffff",
            "font-weight": "700",
          },
          quantityInput: {
            "border-color": "rgba(255,255,255,0.25)",
            color: "#ffffff",
            "background-color": "#121214",
            "-webkit-text-fill-color": "#ffffff",
            "border-radius": "8px",
          },
          subtotalText: {
            color: "#ffffff",
            "text-transform": "uppercase",
            "letter-spacing": "0.02em",
            "font-size": "12px",
          },
          subtotal: {
            color: "#ffffff",
            "font-weight": "800",
            "font-size": "20px",
          },
          notice: {
            color: "rgba(255,255,255,0.65)",
            "font-size": "11px",
          },
          button: {
            background: "linear-gradient(135deg, #ff2b2b, #d00000)",
            "border-radius": "12px",
            "font-weight": "800",
            "text-transform": "uppercase",
            color: "#ffffff",
            padding: "16px 24px",
            width: "100%",
            "letter-spacing": "0.06em",
            "box-shadow": "0 16px 30px rgba(208,0,0,0.32)",
            transition: "transform 0.2s ease, box-shadow 0.2s ease",
          },
          buttonHover: {
            background: "linear-gradient(135deg, #ff3b3b, #ff1a1a)",
            transform: "translateY(-1px)",
            "box-shadow": "0 20px 40px rgba(255,43,43,0.4)",
          },
        },
        toggle: {
          sticky: true,
          styles: {
            toggle: {
              "background-color": "#d00000",
              "border-radius": "999px",
              "box-shadow": "0 8px 20px rgba(208,0,0,0.4)",
            },
          },
        },
      };

      // Si le bouton d'achat existe (page boutique), on crée le composant produit complet
      if (buyButtonTarget) {
        cartComponent = ui.createComponent("product", {
          id: productId,
          node: buyButtonTarget,
          moneyFormat: "%7B%7Bamount_with_comma_separator%7D%7D €",
          options: {
            product: {
              layout: "vertical",
              iframe: false,
              buttonDestination: "cart",
              contents: {
                img: false,
                title: false,
                price: false,
                options: true,
                description: false,
                buttonWithQuantity: false,
                quantity: false,
              },
              text: {
                button: "Ajouter au panier",
              },
              styles: {
                product: {
                  "text-align": "left",
                  background: "transparent",
                  color: "#fff",
                },
                price: {
                  display: "none",
                },
                compareAt: {
                  display: "none",
                },
                variantTitle: {
                  color: "#fff",
                  "font-size": "14px",
                  "margin-bottom": "12px",
                  "text-transform": "uppercase",
                  "letter-spacing": "0.02em",
                },
                button: {
                  "background-color": "#d00000",
                  color: "#fff",
                  "border-radius": "12px",
                  padding: "14px 18px",
                  "font-weight": "700",
                  "letter-spacing": "0.06em",
                  width: "100%",
                  transition: "transform 0.2s ease, background 0.2s ease",
                  "text-transform": "uppercase",
                },
                buttonHover: {
                  "background-color": "#ff1a1a",
                  transform: "translateY(-1px)",
                },
              },
            },
            cart: cartConfig,
            modalProduct: {
              contents: {
                img: true,
                button: true,
                buttonWithQuantity: false,
              },
              styles: {
                button: {
                  "background-color": "#d00000",
                  "border-radius": "12px",
                },
              },
            },
          },
        });
      } else {
        // Sinon, on crée juste le composant produit (invisible) pour avoir le panier,
        // ou on utilise createComponent('cart') si possible, mais Shopify Buy Button JS
        // est souvent lié à un produit. Une astuce est de créer un produit caché ou juste le cart.
        // Ici on va créer le produit mais sans l'afficher, juste pour avoir le toggle du panier.
        // Ou mieux, on utilise createComponent('cart') directement si l'ID n'est pas requis,
        // mais souvent il faut un produit pour initialiser le contexte.
        // Essayons d'initialiser juste le produit mais sans node cible visible,
        // ou mieux: createComponent('product') mais avec un node caché.

        var hiddenNode = document.createElement("div");
        hiddenNode.style.display = "none";
        document.body.appendChild(hiddenNode);

        cartComponent = ui.createComponent("product", {
          id: productId,
          node: hiddenNode,
          moneyFormat: "%7B%7Bamount_with_comma_separator%7D%7D €",
          options: {
            product: {
              iframe: false,
              buttonDestination: "cart",
              contents: {
                img: false,
                title: false,
                price: false,
                options: false,
                description: false,
                button: false, // Pas de bouton
              },
            },
            cart: cartConfig,
          },
        });
      }

      // Intercepter les boutons de checkout après un délai pour que le DOM soit prêt
      setTimeout(function () {
        interceptCheckoutButtons();
        forceQuantityStyles();
      }, 1500);

      // Observer les changements dans le panier pour forcer les styles de quantité
      var cartObserver = new MutationObserver(function () {
        forceQuantityStyles();
      });

      // Observer le conteneur du panier
      setTimeout(function () {
        var cartElement = document.querySelector(".shopify-buy__cart");
        if (cartElement) {
          cartObserver.observe(cartElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ["style", "class"],
          });
        }
      }, 2000);

      // Appel périodique pour s'assurer que les styles sont appliqués
      setInterval(function () {
        forceQuantityStyles();
      }, 1000);
    });
  }

  // Fonction pour forcer les styles blancs sur les éléments de quantité
  function forceQuantityStyles() {
    var quantityElements = document.querySelectorAll(
      ".shopify-buy__cart .shopify-buy__quantity-input, " +
        ".shopify-buy__cart .shopify-buy__cart-item__quantity, " +
        '.shopify-buy__cart [class*="quantity"], ' +
        '.shopify-buy__cart input[type="number"], ' +
        ".shopify-buy__cart .shopify-buy__quantity-container *"
    );

    quantityElements.forEach(function (element) {
      // Forcer la couleur blanche sur tous les éléments de quantité
      if (
        element.tagName === "INPUT" ||
        element.tagName === "SPAN" ||
        element.tagName === "DIV"
      ) {
        element.style.color = "#ffffff";
        element.style.webkitTextFillColor = "#ffffff";
        if (element.tagName === "INPUT") {
          element.style.borderColor = "rgba(255,255,255,0.2)";
          element.style.backgroundColor = "transparent";
        }
      }

      // S'assurer que le texte à l'intérieur est aussi blanc
      var textNodes = element.querySelectorAll("*");
      textNodes.forEach(function (node) {
        if (node.tagName !== "BUTTON" && node.tagName !== "A") {
          node.style.color = "#ffffff";
          node.style.webkitTextFillColor = "#ffffff";
        }
      });
    });

    // Forcer aussi sur les conteneurs de quantité
    var quantityContainers = document.querySelectorAll(
      ".shopify-buy__quantity-container, " +
        ".shopify-buy__cart .shopify-buy__quantity-container, " +
        ".shopify-buy__cart-item__quantity"
    );

    quantityContainers.forEach(function (container) {
      container.style.color = "#ffffff";
      var allChildren = container.querySelectorAll("*");
      allChildren.forEach(function (child) {
        if (
          child.tagName !== "BUTTON" &&
          child.tagName !== "A" &&
          child.tagName !== "INPUT"
        ) {
          child.style.color = "#ffffff";
        }
      });
    });
  }

  function interceptCheckoutButtons() {
    // Observer pour détecter les boutons checkout quand ils apparaissent
    var observer = new MutationObserver(function (mutations) {
      var checkoutButtons = document.querySelectorAll(
        '.shopify-buy__cart__footer button, a[href*="checkout"]'
      );
      checkoutButtons.forEach(function (btn) {
        if (!btn.hasAttribute("data-checkout-intercepted")) {
          btn.setAttribute("data-checkout-intercepted", "true");

          btn.addEventListener(
            "click",
            function (e) {
              e.preventDefault();
              e.stopPropagation();

              // Récupérer l'URL du checkout depuis le bouton ou le panier
              var checkoutUrl = btn.href || btn.getAttribute("href");

              if (!checkoutUrl && cartComponent && cartComponent.cart) {
                // Essayer de récupérer depuis le modèle du panier
                try {
                  var checkoutId = cartComponent.cart.model.checkoutId;
                  if (checkoutId) {
                    checkoutUrl =
                      "https://checkout.shopify.com/carts/" +
                      checkoutId +
                      "/checkout";
                  }
                } catch (err) {
                  console.error(
                    "Erreur lors de la récupération du checkout:",
                    err
                  );
                }
              }

              if (checkoutUrl) {
                openCheckoutModal(checkoutUrl);
              } else {
                // Fallback: attendre un peu et réessayer
                setTimeout(function () {
                  var fallbackUrl =
                    btn.href ||
                    document.querySelector('a[href*="checkout"]')?.href;
                  if (fallbackUrl) {
                    openCheckoutModal(fallbackUrl);
                  }
                }, 500);
              }

              return false;
            },
            true
          );
        }
      });
    });

    // Observer le document entier pour les nouveaux éléments
    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });

    // Intercepter immédiatement les boutons existants
    var existingButtons = document.querySelectorAll(
      '.shopify-buy__cart__footer button, a[href*="checkout"]'
    );
    existingButtons.forEach(function (btn) {
      btn.setAttribute("data-checkout-intercepted", "true");
      btn.addEventListener(
        "click",
        function (e) {
          e.preventDefault();
          e.stopPropagation();
          var checkoutUrl = btn.href || btn.getAttribute("href");
          if (checkoutUrl) {
            openCheckoutModal(checkoutUrl);
          }
          return false;
        },
        true
      );
    });
  }

  if (window.ShopifyBuy) {
    if (window.ShopifyBuy.UI) {
      shopifyBuyInit();
    } else {
      loadScript();
    }
  } else {
    loadScript();
  }
})();
