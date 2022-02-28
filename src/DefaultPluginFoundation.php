<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

/**
 * Define the default plugin foundation.
 */
abstract class DefaultPluginFoundation
{
    /**
     * Define the root path directory level.
     */
    protected const ROOT_PATH_LEVEL = 2;

    /**
     * The plugin banner contents.
     */
    public static function pluginBanner(): ?string
    {
        $bannerPath = static::rootPath() . '/banner.txt';

        if (file_exists($bannerPath)) {
            return file_get_contents(
                $bannerPath
            );
        }

        return null;
    }

    /**
     * The plugin root path.
     *
     * @return string
     *   The plugin root path.
     */
    public static function rootPath(): string
    {
        $filename = (new \ReflectionClass(static::class))
            ->getFileName();
        return dirname($filename, static::ROOT_PATH_LEVEL);
    }

    /**
     * The plugin template directories.
     *
     * @return array
     *   An array of plugin template directories.
     */
    public static function templateDirectories(): array
    {
        return [
            static::rootPath() . '/templates'
        ];
    }

    /**
     * Load the plugin template file.
     *
     * @param string $filename
     *   The template file name.
     *
     * @return null|string
     *   Return the template file contents.
     */
    public static function loadTemplateFile(string $filename): ?string
    {
        if ($filepath = static::getTemplateFilePath($filename)) {
            return file_get_contents($filepath);
        }

        return null;
    }

    /**
     * Retrieve the plugin template file path.
     *
     * @param string $filename
     *   The template file name.
     *
     * @return null|string
     *   Return the fully qualified template path.
     */
    public static function getTemplateFilePath(string $filename): ?string
    {
        foreach (static::templateDirectories() as $directory) {
            $filepath = "{$directory}/{$filename}";

            if (!file_exists($filepath)) {
                continue;
            }

            return $filepath;
        }

        return null;
    }
}
