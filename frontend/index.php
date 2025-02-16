<?php $pageTitle = "Baumarkt-App"; include "components/header.php"; ?>
<main>
    <section id="categories">
        <h2>Kategorien</h2>
        <div id="category-buttons">
            <button class="category-button active" data-category="Werkzeuge">Werkzeuge</button>
            <button class="category-button" data-category="Garten">Garten</button>
            <button class="category-button" data-category="Sanitär">Sanitär</button>
            <button class="category-button" data-category="Elektro">Elektro</button>
            <button class="category-button" data-category="Baustoffe">Baustoffe</button>
        </div>
    </section>
    <section id="produkte">
        <h2>Produkte</h2>
        <p class="produkte-description">Unsere Produkte: Einfach Kategorie auswählen und stöbern!</p>
        <div id="product-list"></div>
    </section>
</main>
<div id="rabatt-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Tolle Rabattaktionen mit 25% !</h2>
        <div id="rabatt-list">
            <!-- Dynamic Content -->
        </div>
    </div>
</div>
<?php include "components/footer.php"; ?>
