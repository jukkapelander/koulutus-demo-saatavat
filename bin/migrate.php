<?php
declare(strict_types=1);

/**
 * Luo skeeman ja lataa testiaineiston. Ajetaan: php bin/migrate.php
 * Tuhoaa olemassa olevat taulut.
 */
require dirname(__DIR__) . '/src/bootstrap.php';

use Saatavat\Db;

$pdo = Db::pdo();
foreach (['schema.sql', 'seed.sql'] as $file) {
    $sql = file_get_contents(dirname(__DIR__) . '/db/' . $file);
    if ($sql === false) {
        fwrite(STDERR, "Tiedostoa db/$file ei voitu lukea\n");
        exit(1);
    }
    $pdo->exec($sql);
    echo "OK  db/$file\n";
}

$count = (int) $pdo->query('SELECT count(*) FROM invoices')->fetchColumn();
echo "Laskuja tietokannassa: $count\n";
