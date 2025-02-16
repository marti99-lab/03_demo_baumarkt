document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get("id");

    if (!productId) {
        document.getElementById("product-title").innerText = "Produkt-ID fehlt!";
        return;
    }

    const pathParts = window.location.pathname.split('/');
    const appBase = pathParts.slice(0, pathParts.indexOf('baumarkt-app') + 1).join('/');
    const baseUrl = `${window.location.origin}${appBase}`;
    const apiUrl = `${baseUrl}/backend/api/products.php?id=${productId}`;

    const productTitle = document.getElementById("product-title");
    const productImage = document.getElementById("product-image");
    const productCategory = document.getElementById("product-category");
    const productDescription = document.getElementById("product-description");
    const availability = document.getElementById("availability");
    const deliveryTime = document.getElementById("delivery-time");
    const shippingInfo = document.getElementById("shipping-info");
    const discountInfo = document.getElementById("discount-info");
    const additionalText = document.getElementById("additional-text");
    const reserveButton = document.getElementById("reserve-pickup");

    function calculateShippingCost(price) {
        return price > 50 ? "Versandkostenfrei" : "Versandkosten: 3.50 €";
    }

    fetch(apiUrl)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Produkt nicht gefunden");
            }
            return response.json();
        })
        .then((product) => {
            // Convert price to a number for correct formatting
            product.price = parseFloat(product.price);

            productTitle.innerText = product.name;
            productImage.src = `${baseUrl}/frontend/assets/images/${product.image}`;
            productImage.alt = product.name;
            productCategory.innerText = product.category;
            productDescription.innerText = `Preis: ${product.price.toFixed(2)} €`;
            deliveryTime.innerText = `Lieferzeit: ${product.lieferzeit}`;
            availability.innerText = `Verfügbarkeit: ${product.availability}`;
            shippingInfo.innerText = calculateShippingCost(product.price);

            if (product.discount > 0) {
                discountInfo.innerText = `Rabatt: ${product.discount}%`;
            } else {
                discountInfo.style.display = "none";
            }

            additionalText.innerText = product.beschreibung || "Keine weiteren Informationen verfügbar.";

            if (product.online_verfügbar === 'nein') {
                reserveButton.style.display = 'none';
            } else {
                reserveButton.style.display = 'block';
                setupReservePopup(product);
            }
        })
        .catch((error) => {
            productTitle.innerText = `Fehler: ${error.message}`;
            additionalText.innerText = "Bitte versuchen Sie es später erneut.";
            productImage.style.display = "none";
        });

    const backButton = document.getElementById("back-button");
    if (backButton) {
        backButton.addEventListener("click", () => {
            window.history.back();
        });
    }

    function setupReservePopup(product) {
        reserveButton.addEventListener("click", () => {
            let modal = document.getElementById("reserve-modal");
            if (!modal) {
                modal = document.createElement("div");
                modal.id = "reserve-modal";
                modal.classList.add("modal");
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <h3>Reservierung bestätigen</h3>
                        <p><strong>${product.name}</strong></p>
                        <p>Preis pro Stück: <strong>${product.price.toFixed(2)} €</strong></p>
                        <label for="reserve-quantity">Menge:</label>
                        <input type="number" id="reserve-quantity" min="1" max="${product.availability}" value="1">
                        <p id="total-reserve-cost">Gesamtpreis: ${product.price.toFixed(2)} €</p>
                        <p style="color: red;">Bitte holen Sie die Ware innerhalb von 3 Tagen ab und bezahlen Sie diese, da ansonsten die Reservierung verfällt.</p>
                        <button id="confirm-reserve" class="btn">Reservierung bestätigen</button>
                    </div>
                `;
                document.body.appendChild(modal);

                const closeModal = modal.querySelector(".close-modal");
                closeModal.addEventListener("click", () => modal.remove());

                const reserveQuantity = modal.querySelector("#reserve-quantity");
                const totalReserveCost = modal.querySelector("#total-reserve-cost");

                reserveQuantity.addEventListener("input", () => {
                    const quantity = Math.max(1, Math.min(reserveQuantity.value || 1, product.availability));
                    reserveQuantity.value = quantity;
                    const totalCost = (quantity * product.price).toFixed(2);
                    totalReserveCost.innerText = `Gesamtpreis: ${totalCost} €`;
                });

                const confirmReserve = modal.querySelector("#confirm-reserve");
                confirmReserve.addEventListener("click", () => {
                    fetch(`${baseUrl}/backend/api/reserve-handler.php`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            product_id: productId,
                            user_id: sessionStorage.getItem('user_id'),
                            quantity: reserveQuantity.value,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Reservierung erfolgreich für ${reserveQuantity.value} Stück(e) von ${product.name}.`);
                            modal.remove();
                            location.reload();
                        } else {
                            alert("Reservierung fehlgeschlagen: " + data.message);
                        }
                    })
                    .catch(() => alert("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut."));
                });
            }

            modal.style.display = 'block';
        });
    }
});
