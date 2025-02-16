document.addEventListener("DOMContentLoaded", () => {
    // Dynamically determine the base URL relative to the current directory
    const pathParts = window.location.pathname.split('/');
    const appBase = pathParts.slice(0, pathParts.indexOf('baumarkt-app') + 1).join('/');
    const baseUrl = `${window.location.origin}${appBase}`;

    // Construct the dynamic API URL
    const apiUrl = `${baseUrl}/backend/api/products.php`;

    // DOM Elements
    const categoryButtons = document.querySelectorAll(".category-button");
    const productList = document.getElementById("product-list");
    const rabattModal = document.getElementById("rabatt-modal");
    const rabattList = document.getElementById("rabatt-list");
    const closeButton = document.querySelector(".close-button");
    const rabattLink = document.getElementById("rabatt-link");

    // Debugging
    console.log("API URL:", apiUrl);

    // Show Rabattaktionen Modal
    const showRabattModal = () => {
        fetch(apiUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((products) => {
                const discountedProducts = products.filter((product) => product.discount > 20);
                // Clear previous list
                rabattList.innerHTML = ""; 

                if (discountedProducts.length > 0) {
                    discountedProducts.forEach((product) => {
                        const productElement = document.createElement("div");
                        productElement.style.display = "flex";
                        productElement.style.alignItems = "center";
                        productElement.style.marginBottom = "10px";

                        productElement.innerHTML = `
                            <img src="assets/images/${product.image}" alt="${product.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                            <a href="product-detail.php?id=${product.id}" style="text-decoration: none; color: #0069d9;">
                                ${product.name}
                            </a>
                        `;
                        rabattList.appendChild(productElement);
                    });
                } else {
                    rabattList.innerHTML = `<p>Keine Produkte mit über 20% Rabatt gefunden.</p>`;
                }

                rabattModal.style.display = "block";
            })
            .catch((error) => {
                rabattList.innerHTML = `<p>Fehler beim Laden der Rabattaktionen: ${error.message}</p>`;
            });
    };

    const closeModal = () => {
        rabattModal.style.display = "none";
    };

    /**
     * Fetch and Display Products
     * @param {string} category - The selected category.
     */
    const fetchProducts = (category = "Werkzeuge") => {
        console.log("Fetching products from:", `${apiUrl}?category=${encodeURIComponent(category)}`);

        fetch(`${apiUrl}?category=${encodeURIComponent(category)}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((products) => {
                // Clear previous products
                productList.innerHTML = ""; 
                products.forEach((product) => {
                    const productCard = document.createElement("div");
                    productCard.className = "product-card";
                    productCard.innerHTML = `
                        <img src="assets/images/${product.image}" alt="${product.name}" class="product-image" />
                        <h4>${product.name}</h4>
                        <p>Kategorie: ${product.category}</p>
                        <p>Preis: ${product.price}€</p>
                        <p>Verfügbarkeit: ${product.availability}</p>
                        <p style="color: ${product.online_verfügbar === "ja" ? "green" : "red"}">
                            ${product.online_verfügbar === "ja" ? "Online verfügbar" : "Online nicht verfügbar"}
                        </p>
                    `;

                    productCard.addEventListener("click", () => {
                        window.location.href = `${baseUrl}/frontend/product-detail.php?id=${product.id}`;
                    });

                    productList.appendChild(productCard);
                });
            })
            .catch((error) => {
                console.error("Error fetching products:", error);
                productList.innerHTML = `<p>Fehler beim Laden der Produkte: ${error.message}</p>`;
            });
    };

    //Initialize Event Listeners
    if (rabattModal && closeButton && rabattLink) {
        rabattLink.addEventListener("click", (event) => {
            event.preventDefault();
            showRabattModal();
        });

        closeButton.addEventListener("click", closeModal);

        window.addEventListener("click", (event) => {
            if (event.target === rabattModal) {
                closeModal();
            }
        });
    }

    if (categoryButtons.length > 0 && productList) {
        // Fetch products for default category on page load
        fetchProducts();

        // Add event listeners to category buttons
        categoryButtons.forEach((button) => {
            button.addEventListener("click", () => {
                // Remove active class from all buttons
                categoryButtons.forEach((btn) => btn.classList.remove("active"));

                // Add active class to the clicked button
                button.classList.add("active");

                // Fetch products for the selected category
                const category = button.getAttribute("data-category");
                fetchProducts(category);
            });
        });
    }
});
