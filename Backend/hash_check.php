<?php
$input = 'lugiaPokemon2015';

$storedHash = '$2y$10$N3s0ECPykav2kgOqFMm/ReK6.7ix4WvEwgafM2I3aWWt3CMcejWAO';

echo "Checking password: '" . htmlspecialchars($input) . "'<br>";
echo "Against stored hash: '" . htmlspecialchars($storedHash) . "'<br><br>";

if (password_verify($input, $storedHash)) {
    echo "Password matches!";
} else {
    echo "Password does NOT match!";
}
?>