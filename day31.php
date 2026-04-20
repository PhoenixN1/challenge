<?php
$pdo = new PDO("mysql:host=localhost;dbname=ecole;charset=utf8","root","");

$pdo->exec("CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
) ENGINE=MyISAM");

$pdo->exec("CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE,
    email VARCHAR(150),
    id_classe INT
) ENGINE=MyISAM");

$pdo->exec("CREATE TABLE enseignants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150)
) ENGINE=MyISAM");

$pdo->exec("CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
) ENGINE=MyISAM");

$pdo->exec("CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_enseignant INT,
    id_matiere INT,
    id_classe INT
) ENGINE=MyISAM");

$pdo->exec("CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT,
    id_matiere INT,
    note FLOAT
) ENGINE=MyISAM");
?>```
