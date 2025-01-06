<?php

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Début du script<br>";

// Connexion à la base de données
$servername = "localhost";
$username = "tifosi";
$password = "motdepasse123";
$dbname = "tifosi";

echo "Tentative de connexion à la base de données...<br>";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion failed: " . $conn->connect_error);
}

// Définir l'encodage de la connexion en UTF-8
$conn->set_charset("utf8mb4");

echo "Connexion à la base de données réussie<br>";

// Chemin vers le fichier CSV à importer
$file = './focaccia.csv';

echo "Chemin du fichier CSV: $file<br>";

// Vérification si le fichier existe
if (!file_exists($file)) {
    echo "Le fichier CSV n'existe pas à l'emplacement spécifié: " . $file . "<br>";
    exit; 
}

echo "Fichier CSV trouvé<br>";

// Ouverture du fichier
$handle = fopen($file, "r");

if (!$handle) {
    echo "Impossible d'ouvrir le fichier CSV: " . $file . "<br>";
    exit; 
}

echo "Fichier CSV ouvert<br>";

// Ignorer la première ligne (en-têtes)
if (!fgetcsv($handle, 1000, ",")) {
    echo "Impossible de lire la première ligne du fichier CSV.<br>";
    exit; 
}

echo "Première ligne ignorée<br>";

// Lecture du fichier ligne par ligne
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) < 4) {
        echo "Ligne invalide ignorée: " . implode(",", $data) . "<br>";
        continue;
    }

    $id_focaccia = $data[0];
    $nom_focaccia = $data[1];
    $prix_focaccia = $data[2];
    $ingredients = $data[3];

    // Vérification que id_focaccia est un entier valide
    if (!is_numeric($id_focaccia)) {
        echo "Valeur invalide pour id_focaccia: " . $id_focaccia . "<br>";
        continue;
    }

    echo "Lecture de la ligne: id_focaccia=$id_focaccia, nom_focaccia=$nom_focaccia, prix_focaccia=$prix_focaccia, ingredients=$ingredients<br>";

    // Requête SQL pour insérer dans la table focaccia
    $sql_focaccia = "INSERT INTO focaccia (id_focaccia, nom_focaccia, prix_focaccia) VALUES ('$id_focaccia', '$nom_focaccia', '$prix_focaccia')";

    // Exécution de la requête pour la table focaccia
    if ($conn->query($sql_focaccia) === TRUE) {
        echo "Enregistrement effectué pour id_focaccia: $id_focaccia, nom_focaccia: $nom_focaccia, prix_focaccia: $prix_focaccia<br>";

        // Insérer les relations dans la table comprend
        $ingredient_array = explode(", ", $ingredients);
        foreach ($ingredient_array as $ingredient_nom) {
            // Requête SQL pour obtenir l'id_ingredient
            $sql_ingredient = "SELECT id_ingredient FROM ingredient WHERE nom_ingredient = '$ingredient_nom'";
            $result = $conn->query($sql_ingredient);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_ingredient = $row['id_ingredient'];

                // Requête SQL pour insérer dans la table comprend
                $sql_comprend = "INSERT INTO comprend (id_focaccia, id_ingredient) VALUES ('$id_focaccia', '$id_ingredient')";

                // Exécution de la requête pour la table comprend
                if ($conn->query($sql_comprend) === TRUE) {
                    echo "Enregistrement effectué pour id_focaccia: $id_focaccia, id_ingredient: $id_ingredient dans la table comprend<br>";
                } else {
                    echo "Error: " . $sql_comprend . "<br>" . $conn->error . "<br>";
                }
            } else {
                echo "Ingrédient non trouvé: $ingredient_nom<br>";
            }
        }
    } else {
        echo "Error: " . $sql_focaccia . "<br>" . $conn->error . "<br>";
    }
}

// Fermeture du fichier
fclose($handle);

echo "Fichier CSV fermé<br>";

// Fermeture de la connexion
$conn->close();

echo "Connexion à la base de données fermée<br>";

?>
