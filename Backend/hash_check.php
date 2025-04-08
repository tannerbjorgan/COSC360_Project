<?php
$input = 'lugiaPokemon2015';

$storedHash = '$2y$10$4ib5U5764wXAW2JW8tLSW.5yIX3NSDjtcK8UeWQl7QWa16c062GxS';

echo "Checking password: '" . htmlspecialchars($input) . "'<br>";
echo "Against stored hash: '" . htmlspecialchars($storedHash) . "'<br><br>";

if (password_verify($input, $storedHash)) {
    echo "Password matches!";
} else {
    echo "Password does NOT match!";
}
?>