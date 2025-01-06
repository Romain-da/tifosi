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

    // Utiliser la base de données tifosi
    $conn->query("USE tifosi");

    // Requêtes SQL pour créer les tables
    $sql_queries = [
        "CREATE TABLE client (
            id_client INT PRIMARY KEY AUTO_INCREMENT,
            nom_client VARCHAR(45) NOT NULL,
            age INT,
            cp_client INT
        );"
        "CREATE TABLE achete (
            id_achete INT PRIMARY KEY AUTO_INCREMENT,
            id_client INT,
            jour DATE,
            FOREIGN KEY (id_client) REFERENCES client(id_client)
        );"
        "CREATE TABLE paye (
            id_paye INT PRIMARY KEY AUTO_INCREMENT,
            id_achete INT,
            jour DATE,
            FOREIGN KEY (id_achete) REFERENCES achete(id_achete)
        );"
        "CREATE TABLE focaccia (
            id_focaccia INT PRIMARY KEY AUTO_INCREMENT,
            nom_focaccia VARCHAR(45) NOT NULL,
            prix_focaccia FLOAT
        );"
        "CREATE TABLE comprend (
            id_comprend INT PRIMARY KEY AUTO_INCREMENT,
            id_focaccia INT,
            id_ingredient INT,
            FOREIGN KEY (id_focaccia) REFERENCES focaccia(id_focaccia)
        );"
        "CREATE TABLE ingredient (
            id_ingredient INT PRIMARY KEY AUTO_INCREMENT,
            nom_ingredient VARCHAR(45) NOT NULL
        );"
        "CREATE TABLE menu (
            id_menu INT PRIMARY KEY AUTO_INCREMENT,
            nom_menu VARCHAR(45) NOT NULL,
            prix_menu FLOAT
        );"
        "CREATE TABLE est_constitue (
            id_est_constitue INT PRIMARY KEY AUTO_INCREMENT,
            id_menu INT,
            id_focaccia INT,
            FOREIGN KEY (id_menu) REFERENCES menu(id_menu),
            FOREIGN KEY (id_focaccia) REFERENCES focaccia(id_focaccia)
        );"
        "CREATE TABLE contenir (
            id_contenir INT PRIMARY KEY AUTO_INCREMENT,
            id_menu INT,
            id_boisson INT,
            FOREIGN KEY (id_menu) REFERENCES menu(id_menu)
        );"
        "CREATE TABLE boisson (
            id_boisson INT PRIMARY KEY AUTO_INCREMENT,
            nom_boisson VARCHAR(45) NOT NULL
        );"
        "CREATE TABLE marque (
            id_marque INT PRIMARY KEY AUTO_INCREMENT,
            nom_marque VARCHAR(45) NOT NULL
        );"
        "CREATE TABLE appartient (
            id_appartient INT PRIMARY KEY AUTO_INCREMENT,
            id_boisson INT,
            id_marque INT,
            FOREIGN KEY (id_boisson) REFERENCES boisson(id_boisson),
            FOREIGN KEY (id_marque) REFERENCES marque(id_marque)
        );"
        ];

        // Exécuter les requêtes SQL
        foreach ($sql_queries as $sql) {
            if ($conn->query($sql) === TRUE) {
                echo "Table créée avec succès.<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
            }
    }

    // Fermeture de la connexion
    $conn->close();

?>