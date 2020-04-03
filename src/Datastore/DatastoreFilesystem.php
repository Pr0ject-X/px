<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Datastore;

/**
 * Define the datastore filesystem base class.
 */
abstract class DatastoreFilesystem implements DatastoreFilesystemInterface
{
    /**
     * @var \SplFileObject
     */
    protected $datastoreFile;

    /**
     * Define the datastore file constructor.
     *
     * @param string $filename
     *   The file path where the data resides.
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            $fileDirectory = dirname($filename);

            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0775, true);
            }
            touch($filename);
        }
        $this->datastoreFile = new \SplFileObject($filename);
    }

    /**
     * {@inheritDoc}
     */
    public function read()
    {
        return $this->transformOutput(
            file_get_contents(
                $this->getDatastorePath()
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function write($content): bool
    {
        return file_put_contents(
            $this->getDatastorePath(),
            $this->transformInput($content)
        ) !== false;
    }

    /**
     * Get the datastore filesystem path.
     *
     * Only needed due to the vfsStream not being able
     * to test using the splFileInfo::getRealPath() method.
     *
     * @return string
     *   The path to the datastore file.
     */
    protected function getDatastorePath(): string
    {
        $fileObject = $this->datastoreFile;

        return $fileObject->getRealPath() !== false
            ? $fileObject->getRealPath()
            : "{$fileObject->getPath()}/{$fileObject->getFilename()}";
    }

    /**
     * Transform the content before the input is written.
     *
     * @param $content
     *   The content to transform.
     *
     * @return string|array
     */
    abstract protected function transformInput($content);

    /**
     * Transform the content before the output is read.
     *
     * @param $content
     *   The content to transform.
     *
     * @return string|array
     */
    abstract protected function transformOutput($content);
}
