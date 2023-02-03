<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require 'vendor/autoload.php';

// Lire le fichier Excel
$spreadsheet = IOFactory::load('test.xlsx');

// Récupérer la première feuille
$worksheet = $spreadsheet->getActiveSheet();

// Obtenir les données de la feuille sous forme de tableau
$data = $worksheet->toArray();

// Connexion à la base de données
$conn = new PDO('mysql:host=localhost;dbname=test', 'username=root', 'password');

// Préparer une requête pour insérer les données dans la table
$stmt = $conn->prepare("INSERT INTO table_name (nom_du_groupe, origine, ville, annee_debut, annee_separation, fondateurs, membres, courant_musical, presentation)
VALUES (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9)");

// Boucle sur les lignes de données
foreach ($data as $row) {
    // Affecter les valeurs à la requête
    $stmt->bindValue(':col1', $row[1]);
    $stmt->bindValue(':col2', $row[2]);
    $stmt->bindValue(':col3', $row[3]);
    $stmt->bindValue(':col4', $row[4]);
    $stmt->bindValue(':col5', $row[5]);
    $stmt->bindValue(':col6', $row[6]);
    $stmt->bindValue(':col7', $row[7]);
    $stmt->bindValue(':col8', $row[8]);
    $stmt->bindValue(':col9', $row[9]);

    // Exécuter la requête
    $stmt->execute();
}

// Afficher un message de confirmation
echo "Les données ont été importées avec succès!";
