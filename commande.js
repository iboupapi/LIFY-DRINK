document.addEventListener("DOMContentLoaded", function () {
    const addJusButton = document.getElementById("addJus");
    const jusContainer = document.getElementById("jus-container");
    const prixTotal = document.getElementById("prix");

    // Fonction pour mettre à jour le prix total
    function updatePrice() {
        let total = 0;
        document.querySelectorAll(".jus-block").forEach(block => {
            let quantity = block.querySelector(".quantity").value;
            let taille = block.querySelector(".taille").value;
            let price = taille === "250cl" ? 300 : taille === "1000ml" ? 1000 : 0;
            total += price * quantity;
        });
        prixTotal.textContent = total;
    }

    // Ajouter un nouveau bloc de jus
    addJusButton.addEventListener("click", function () {
        const newBlock = document.createElement("div");
        newBlock.classList.add("jus-block");
        newBlock.innerHTML = `
            <label for="jus">Jus</label>
            <select name="jus[]" class="jus">
                <option value=''>---------</option>
                <option value="Bissap">Bissap</option>
                <option value="Bouye">Bouye</option>
                <option value="Gingembre">Gingembre</option>
            </select>

            <label for="quantity">Quantité</label>
            <input type="number" value="1" name="quantity[]" class="quantity" min="1">

            <label for="taille">Taille</label>
            <select name="taille[]" class="taille">
                <option value=''>-------</option>
                <option value="250cl">250cl</option>
                <option value="1000ml">1L</option>
            </select>

            <button type="button" class="removeJus"> Supprimer</button>
        `;

        // Ajouter un event listener pour supprimer le bloc
        newBlock.querySelector(".removeJus").addEventListener("click", function () {
            newBlock.remove();
            updatePrice();
        });

        // Ajouter le bloc et mettre à jour le prix
        jusContainer.appendChild(newBlock);
        updatePrice();
    });

    // Mettre à jour le prix quand on change quantité ou taille
    jusContainer.addEventListener("input", updatePrice);
});
