<?php

require_once __DIR__.'/functions/pdo_init.php';

try {
    $pdo = get_pdo_connection();

    // Generate and insert 1000 random user records
    for ($i = 0; $i < 100000; $i++) {
        $username = generateRandomString(8);  // Generate random username
        $email = generateRandomString(8) . '@example.com';  // Generate random email

        $confirmed = (mt_rand(1, 100) <= 15) ? 1 : 0;  // 15% chance of confirmed = 1
        $validts = ($confirmed === 0 || mt_rand(1, 100) <= 80) ? 0 : strtotime("now +2 days") + 6*60*60;
        $checked = 0;
        $valid = ($confirmed === 1) ? 0 : ((mt_rand(1, 100) <= 80) ? 0 : 1);  // 80% chance of valid = 0 if not confirmed

        $sql = "INSERT INTO users (username, email, validts, confirmed, checked, valid) 
                VALUES (:username, :email, :validts, :confirmed, :checked, :valid)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':validts', $validts);
        $stmt->bindParam(':confirmed', $confirmed);
        $stmt->bindParam(':checked', $checked);
        $stmt->bindParam(':valid', $valid);

        $stmt->execute();

        print $i . PHP_EOL;

        // 2.5 hours. / 60 workers. ( 3075 sends )
    }
    echo "Data inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Function to generate a random string of a specified length
function generateRandomString($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}