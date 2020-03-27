<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

/**
 * Define the test directory trait commands.
 */
trait TestDirectoryTrait
{
    /**
     * @var string
     */
    protected $testDirectory;

    /**
     * Set the test directory.
     *
     * @param string $directory
     *   The test directory path.
     */
    protected function setTestDirectory(string $directory): void
    {
        $this->testDirectory = $directory;
    }

    /**
     * Create the test directory.
     */
    protected function createTestDirectory(): void
    {
        if (file_exists($this->testDirectory)) {
            $this->cleanTestDirectory();
        }

        $this->localFilesystem->mkdir($this->testDirectory);
    }

    /**
     * Clean the test directory.
     */
    protected function cleanTestDirectory(): void
    {
        if (file_exists($this->testDirectory)) {
            $this->localFilesystem->remove($this->testDirectory);
        }
    }

    /**
     * Get the test directory file contents.
     *
     * @param string $filename
     *   The test directory filename.
     *
     * @return string
     *    The file content contained within test directory.
     */
    protected function getTestDirectoryFileContents(
        string $filename
    ): string {
        $filePath = "{$this->testDirectory}/{$filename}";

        return file_exists($filePath)
            ? file_get_contents($filePath)
            : "";
    }
}
