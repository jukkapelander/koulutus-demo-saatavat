<?php
declare(strict_types=1);

namespace Saatavat;

final class Http
{
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), PHP_EOL;
        exit;
    }

    /** @return array<string, mixed> */
    public static function body(): array
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw === false || $raw === '' ? '[]' : $raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<string, string> */
    public static function headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = (string) $value;
            }
        }
        return $headers;
    }
}
