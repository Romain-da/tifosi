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

echo "Connexion à la base de données réussie<br>";

// Chemin vers le fichier CSV à importer
$file = './marque.csv';

echo "Chemin du fichier CSV: $file<br>";

// Vérification si le fichier existe
if (!file_exists($file)) {
    echo "Le fichier CSV n'existe pas à l'emplacement spécifié: " . $file . "<br>";
    exit; // Utiliser exit pour arrêter l'exécution du script
}

echo "Fichier CSV trouvé<br>";

// Ouverture du fichier
$handle = fopen($file, "r");

if (!$handle) {
    echo "Impossible d'ouvrir le fichier CSV: " . $file . "<br>";
    exit; // Utiliser exit pour arrêter l'exécution du script
}

echo "Fichier CSV ouvert<br>";

// Ignorer la première ligne (en-têtes)
if (!fgetcsv($handle, 1000, ",")) {
    echo "Impossible de lire la première ligne du fichier CSV.<br>";
    exit; // Utiliser exit pour arrêter l'exécution du script
}

echo "Première ligne ignorée<br>";

// Lecture du fichier ligne par ligne
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) < 2) {
        echo "Ligne invalide ignorée: " . implode(",", $data) . "<br>";
        continue;
    }

    $id_marque = $data[0];
    $nom_marque = $data[1];

    // Vérification que id_marque est un entier valide
    if (!is_numeric($id_marque)) {
        echo "Valeur invalide pour id_marque: " . $id_marque . "<br>";
        continue;
    }

    echo "Lecture de la ligne: id_marque=$id_marque, nom_marque=$nom_marque<br>";

    // Requête SQL
    $sql = "INSERT INTO marque (id_marque, nom_marque) VALUES ('$id_marque', '$nom_marque')";

    // Exécution de la requête
    if ($conn->query($sql) === TRUE) {
        echo "Enregistrement effectué pour id_marque: $id_marque, nom_marque: $nom_marque<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
    }
}

// Fermeture du fichier
fclose($handle);

echo "Fichier CSV fermé<br>";

// Fermeture de la connexion
$conn->close();

echo "Connexion à la base de données fermée<br>";

?>
