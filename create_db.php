<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3307', 'root', '');
$pdo->exec('CREATE DATABASE IF NOT EXISTS sistem_deadstock');
echo "Database created\n";
