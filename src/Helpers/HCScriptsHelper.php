<?php

declare(strict_types = 1);

namespace HoneyComb\Scripts\Helpers;


/**
 * Class HCFrontendResponse
 * @package HoneyComb\Scripts\Helpers
 */
class HCScriptsHelper
{
    /**
     * Replaceable symbols
     *
     * @var array
     */
    protected $toReplace = ['.', '_', '/', ' ', '-', ':'];

    /**
     * Create folder recursively if not exists.
     *
     * @param string $path
     * @return bool
     */
    public function createDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }

        return true;
    }

    /**
     * @param string $message
     */
    public function abort (string $message)
    {
        throw new \Exception($message);
    }

    /**
     * Remove all items from string
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    public function stringOnly(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '', trim($string, '/'));
    }

    /**
     * Get replaceable symbols
     *
     * @param array $ignoreSymbols
     * @return array
     */
    public function getToReplace(array $ignoreSymbols): array
    {
        if (empty($ignoreSymbols)) {
            return $this->toReplace;
        }

        return array_diff($this->toReplace, $ignoreSymbols);
    }

    /**
     * Replace file
     * @param string $destination
     * @param string $templateLocation
     * @param array $content
     * @param bool $createFile
     * @return string
     */
    public function createFileFromTemplate(string $destination, string $templateLocation, array $content, bool $createFile = true): string
    {
        $destination = replaceBrackets($destination, $content);

        $template = file_get_contents(__DIR__ . '/../resources/templates/' . $templateLocation);

        $template = replaceBrackets($template, $content);

        if (!$createFile)
            return $template;

        $directory = array_filter(explode('/', $destination));
        array_pop($directory);
        $directory = implode('/', $directory);

        $this->createDirectory($directory);
        file_put_contents($destination, $template);

        return "";
    }

    /**
     * Make string in dot from slashes
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    public function stringWithDots(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '.', $string);
    }

    /**
     * Get string in underscore
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    public function stringWithUnderscore(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '_', trim($string, '/'));
    }

    /**
     * Getting hc-config of the package
     *
     * @param string $directory
     * @return array
     */
    public function getHCConfig(string $directory): array
    {
        return json_decode(file_get_contents( $directory . '/hc-config.json'), true);
    }

    /**
     * Updating hc-config package file
     *
     * @param string $directory
     * @param array $config
     */
    public function setHCConfig(string $directory, array $config)
    {
        file_put_contents($directory . '/hc-config.json',
            json_encode($config, JSON_PRETTY_PRINT));
    }
}
