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
$file = './boisson.csv';

echo "Chemin du fichier CSV: $file<br>";

// Vérification si le fichier existe
if (!file_exists($file)) {
    echo "Le fichier CSV n'existe pas à l'emplacement spécifié: " . $file . "<br>";
    exit; 

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

echo "Première ligne ignorée<br>";

// Lecture du fichier ligne par ligne
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) < 3) {
        echo "Ligne invalide ignorée: " . implode(",", $data) . "<br>";
        continue;
    }

    $id_boisson = $data[0];
    $nom_boisson = $data[1];
    $nom_marque = $data[2];

    // Vérification que id_boisson est un entier valide
    if (!is_numeric($id_boisson)) {
        echo "Valeur invalide pour id_boisson: " . $id_boisson . "<br>";
        continue;
    }

    echo "Lecture de la ligne: id_boisson=$id_boisson, nom_boisson=$nom_boisson, nom_marque=$nom_marque<br>";

    // Requête SQL pour insérer dans la table boisson
    $sql_boisson = "INSERT INTO boisson (id_boisson, nom_boisson) VALUES ('$id_boisson', '$nom_boisson')";

    // Exécution de la requête pour la table boisson
    if ($conn->query($sql_boisson) === TRUE) {
        echo "Enregistrement effectué pour id_boisson: $id_boisson, nom_boisson: $nom_boisson<br>";

        // Requête SQL pour insérer dans la table appartient
        $sql_appartient = "INSERT INTO appartient (id_boisson, id_marque) VALUES ('$id_boisson', (SELECT id_marque FROM marque WHERE nom_marque = '$nom_marque'))";

        // Exécution de la requête pour la table appartient
        if ($conn->query($sql_appartient) === TRUE) {
            echo "Enregistrement effectué pour id_boisson: $id_boisson, nom_marque: $nom_marque dans la table appartient<br>";
        } else {
            echo "Error: " . $sql_appartient . "<br>" . $conn->error . "<br>";
        }
    } else {
        echo "Error: " . $sql_boisson . "<br>" . $conn->error . "<br>";
    }
}

// Fermeture du fichier
fclose($handle);

echo "Fichier CSV fermé<br>";

// Fermeture de la connexion
$conn->close();

echo "Connexion à la base de données fermée<br>";

?>
