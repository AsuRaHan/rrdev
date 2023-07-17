<?php
namespace rrdev;
/**
 * Класс для управления конфигурацией приложения.
 */
class Config
{
    /**
     * Массив, содержащий конфигурацию приложения.
     *
     * @var array
     */
    private static $config;

    /**
     * Инициализирует конфигурацию приложения из файла.
     *
     * @return void
     */
    public static function init(): void
    {
        self::$config = require_once CONFIG_DIR . 'app.php';
    }

    /**
     * Возвращает значение конфигурации, связанное с указанным ключом.
     *
     * @param string $key Ключ конфигурации в виде строки, например "database.host".
     * @return mixed Значение конфигурации, связанное с ключом, или null, если ключ не найден.
     */
    public static function get(string $key)
    {
        $keys = explode('.', $key);
        $value = self::$config;
        foreach ($keys as $subkey) {
            if (isset($value[$subkey])) {
                $value = $value[$subkey];
            } else {
                return null;
            }
        }
        return $value;
    }

    /**
     * Устанавливает значение конфигурации для указанного ключа.
     *
     * @param string $key Ключ конфигурации в виде строки, например "database.host".
     * @param mixed $value Значение конфигурации для установки.
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $ref = &self::$config;
        foreach ($keys as $subkey) {
            if (!isset($ref[$subkey])) {
                $ref[$subkey] = [];
            }
            $ref = &$ref[$subkey];
        }
        $ref = $value;
    }
}