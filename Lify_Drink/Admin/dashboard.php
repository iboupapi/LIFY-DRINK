<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - LIFY DRINK</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
    <input type="text" id="searchInput" placeholder="Rechercher une commande...">

        <h1>Tableau de bord - LIFY DRINK</h1>

        <!-- Section des commandes -->
        <section>
            <h2>Commandes récentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Client</th>
                        <th>Jus</th>
                        <th>Quantité</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Connexion à la base de données
                    $pdo = new PDO("mysql:host=127.0.0.1;dbname=gestion_commande", "root", "");
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Récupérer les commandes récentes
                    $stmt = $pdo->query("
                        SELECT c.id_commande, cl.Nom AS client, c.jus, c.Quantite
                        FROM commande c
                        JOIN client cl ON c.Id_client = cl.Id_client
                        ORDER BY c.date_commande DESC
                        LIMIT 10
                    ");
                    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Afficher les commandes
                    foreach ($commandes as $commande) {
                        echo "<tr>
                                <td>{$commande['id_commande']}</td>
                                <td>{$commande['client']}</td>
                                <td>{$commande['jus']}</td>
                                <td>{$commande['Quantite']}</td>
                                <td>{$commande['date_commande']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Section des clients -->
        <section>
            <h2>Clients récents</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Client</th>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Adresse</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Récupérer les clients récents
                    $stmt = $pdo->query("SELECT * FROM client ORDER BY Id_client DESC LIMIT 10");
                    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Afficher les clients
                    foreach ($clients as $client) {
                        echo "<tr>
                                <td>{$client['Id_client']}</td>
                                <td>{$client['Nom']}</td>
                                <td>{$client['Contact']}</td>
                                <td>{$client['Adresse']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Section des produits -->
        <section>
            <h2>Produits</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Produit</th>
                        <th>Nom</th>
                        <th>Prix (250cl)</th>
                        <th>Prix (1000ml)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Récupérer les produits
                    $stmt = $pdo->query("SELECT * FROM produit");
                    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Afficher les produits
                    foreach ($produits as $produit) {
                        echo "<tr>
                                <td>{$produit['id']}</td>
                                <td>{$produit['nom']}</td>
                                <td>{$produit['prix_250cl']} FCFA</td>
                                <td>{$produit['prix_1000ml']} FCFA</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
    </script>
</body>
</html>