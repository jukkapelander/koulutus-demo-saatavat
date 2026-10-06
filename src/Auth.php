<?php
declare(strict_types=1);

namespace Saatavat;

final class Auth
{
    /**
     * Tunnistaa API-avaimen perusteella asiakasyrityksen (velkojan), jonka nimissä pyyntö tehdään.
     *
     * @return array<string, mixed>|null companies-taulun rivi tai null
     */
    public static function authenticate(): ?array
    {
        // Avain otetaan ensisijaisesti otsakkeesta; query-parametri on tuettu vanhojen integraatioiden takia.
        $key = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? null);
        if (!is_string($key) || $key === '') {
            return null;
        }
        $statement = Db::pdo()->prepare('SELECT * FROM companies WHERE api_key = :key');
        $statement->execute([':key' => $key]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }
}
