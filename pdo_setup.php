<?php
    try {
    $pdo = new PDO("mysql:host=localhost;dbname=groceriesdb", 'root', '');
} catch (PDOException $pe) {
    die("Could not connect to the database groceriesdb:" . $pe->getMessage());
}