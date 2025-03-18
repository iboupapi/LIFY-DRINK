<?php

// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'gestion_commande';
$username = 'root'; // Remplacez par votre nom d'utilisateur MySQL
$password = ''; // Remplacez par votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour nettoyer les données
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Validation et récupération des données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $nom = cleanInput($_POST['name']);
    $adresse = cleanInput($_POST['adresse']);
    $tel = cleanInput($_POST['tel']);
    $jus = $_POST['jus'];
    $quantites = $_POST['quantity'];
    $tailles = $_POST['taille'];

    // Validation des champs obligatoires
    $errors = [];
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    if (empty($adresse)) {
        $errors[] = "L'adresse est obligatoire.";
    }
    if (empty($tel)) {
        $errors[] = "Le téléphone est obligatoire.";
    }
    if (empty($jus) || !is_array($jus)) {
        $errors[] = "Veuillez sélectionner au moins un jus.";
    }

    // Si des erreurs sont détectées, on les affiche
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
        exit();
    }

    // Début de la transaction
    $pdo->beginTransaction();

    try {
        // Insertion du client dans la table `client`
        $stmtClient = $pdo->prepare("INSERT INTO client (Nom, Contact, Adresse) VALUES (:nom, :contact, :adresse)");
        $stmtClient->execute([
            ':nom' => $nom,
            ':contact' => $tel,
            ':adresse' => $adresse
        ]);
        $idClient = $pdo->lastInsertId(); // Récupère l'ID du client inséré

        // Insertion de la commande dans la table `commande`
        $stmtCommande = $pdo->prepare("INSERT INTO commande (Quantite, Id_client, jus) VALUES (:quantite, :id_client, :jus)");
        $stmtCommandeDetail = $pdo->prepare("INSERT INTO commande_detail (quantite, taille, prix) VALUES (:quantite, :taille, :prix)");

        // Boucle pour traiter chaque jus commandé
        foreach ($jus as $index => $jusNom) {
            $quantite = intval($quantites[$index]);
            $taille = cleanInput($tailles[$index]);

            // Validation de la quantité et de la taille
            if ($quantite < 1) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }

            // Validation spécifique pour la taille 250cl
            if ($taille === '250cl' && $quantite < 50) {
                throw new Exception("Pour la taille 250cl, la quantité minimale est de 50.");
            }

            if (!in_array($taille, ['250cl', '1000ml'])) {
                throw new Exception("Taille de jus invalide.");
            }

            // Définir le prix unitaire en fonction de la taille
            if ($taille === '250cl') {
                $prixUnitaire = 300; // Prix unitaire fixe pour 250cl
            } else {
                // Récupération du prix pour 1000ml depuis la base de données
                $stmtProduit = $pdo->prepare("SELECT prix_1000ml FROM produit WHERE nom = :nom");
                $stmtProduit->execute([':nom' => $jusNom]);
                $produit = $stmtProduit->fetch(PDO::FETCH_ASSOC);

                if (!$produit) {
                    throw new Exception("Le jus '$jusNom' n'existe pas dans la base de données.");
                }

                $prixUnitaire = $produit['prix_1000ml'];
            }

            // Calcul du prix total pour ce jus
            $prixTotal = $prixUnitaire * $quantite;

            // Insertion de la commande principale
            $stmtCommande->execute([
                ':quantite' => $quantite,
                ':id_client' => $idClient,
                ':jus' => $jusNom
            ]);
            $idCommande = $pdo->lastInsertId(); // Récupère l'ID de la commande insérée

            // Insertion des détails de la commande
            $stmtCommandeDetail->execute([
                ':quantite' => $quantite,
                ':taille' => $taille,
                ':prix' => $prixTotal
            ]);
        }

        // Valider la transaction
        $pdo->commit();


       // Redirection vers la page de notification
        header("Location: confirmation.php");
        exit();
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
        echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
        exit();
    }
} else {
    echo "<p>Aucune donnée reçue.</p>";
}
?>