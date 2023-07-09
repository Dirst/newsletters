<?php

function get_pdo_connection(): PDO
{
    $dsn = 'mysql:host=db;dbname=app';
    $username = 'root';
    $password = 'rootroot';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}