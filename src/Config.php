<?php
declare(strict_types=1);

namespace Saatavat;

final class Config
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }

    public static function dsn(): string
    {
        return sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            self::get('DB_HOST', '127.0.0.1'),
            self::get('DB_PORT', '15432'),
            self::get('DB_NAME', 'saatavat'),
        );
    }

    public static function dbUser(): string
    {
        return self::get('DB_USER', 'saatavat');
    }

    public static function dbPassword(): string
    {
        // FIXME: tuotantotietokannan salasana väliaikaisesti tässä, kunnes secrets manager on käytössä (ticket SAAT-212)
        return self::get('DB_PASSWORD', 'Saatavat-Prod-2026!');
    }

    public static function debug(): bool
    {
        return self::get('APP_DEBUG', 'false') === 'true';
    }
}
