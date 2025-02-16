document.addEventListener("DOMContentLoaded", function () {
    const reserveButton = document.getElementById("reserve-pickup");
    const modal = document.getElementById("reservation-modal");
    const closeButton = document.querySelector(".close-button");
    const quantityInput = document.getElementById("reservation-quantity");
    const totalCostDisplay = document.getElementById("reservation-total-cost");
    const confirmButton = document.getElementById("confirm-reservation");

    // Ensure product price is retrieved safely
    const productPrice = parseFloat(document.getElementById("product-price").value);

    // Show the modal when the reserve button is clicked
    reserveButton.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // Close the modal
    closeButton.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Update the total cost dynamically when the quantity changes
    quantityInput.addEventListener("input", () => {
        let quantity = parseInt(quantityInput.value) || 1;
        const maxQuantity = parseInt(quantityInput.max) || 1;

        // Ensure quantity stays within valid bounds
        if (quantity < 1) quantity = 1;
        if (quantity > maxQuantity) quantity = maxQuantity;

        quantityInput.value = quantity;
        const totalCost = (quantity * productPrice).toFixed(2);
        totalCostDisplay.textContent = `Gesamtpreis: ${totalCost} €`;
    });

    // Handle reservation confirmation
    confirmButton.addEventListener("click", () => {
        const quantity = parseInt(quantityInput.value) || 1;

        // Perform an AJAX request to store the reservation
        fetch("../backend/api/reserve-handler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                user_id: document.getElementById("user-id").value,
                product_id: document.getElementById("product-id").value,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Reservierung erfolgreich! Verfügbarkeit wurde aktualisiert.");
                window.location.reload(); // Refresh the page to show updated availability
            } else {
                alert(data.message || "Fehler bei der Reservierung.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Fehler beim Verarbeiten der Reservierung.");
        });
    });

    // Close the modal if the user clicks outside it
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
