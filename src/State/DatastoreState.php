<?php

declare(strict_types=1);

namespace Pr0jectX\Px\State;

use Pr0jectX\Px\Datastore\DatastoreFile;
use Pr0jectX\Px\Datastore\DatastoreFilesystemInterface;
use Pr0jectX\Px\State\FileDatastore;

/**
 * Define the datastore state.
 */
class DatastoreState implements StateInterface
{
    /**
     * @var array
     */
    protected $state;

    /**
     * @var \Pr0jectX\Px\Datastore\DatastoreFile
     */
    protected $store;

    /**
     * Define the datastore state constructor.
     *
     * @param \Pr0jectX\Px\Datastore\DatastoreFilesystemInterface $datastore
     */
    public function __construct(DatastoreFilesystemInterface $datastore)
    {
        $this->store = $datastore;
        $this->state = (array) $datastore->read();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key = null)
    {
        if (!isset($key)) {
            return $this->state;
        }
        $keys = !is_array($key) ? [$key] : $key;

        return $this->getNestedDataValue($keys, $this->state);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value): DatastoreState
    {
        $this->state[$key] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function del(string $key): DatastoreState
    {
        unset($this->state[$key]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function save(): bool
    {
        return $this->store->write($this->state);
    }

    /**
     * Retrieve the nested data value.
     *
     * @param array $keys
     *   An array of data nested key structure.
     * @param array $data
     *   An array of data to search within.
     *
     * @return mixed
     *   Return nested values; otherwise false if an error occurred.
     */
    protected function getNestedDataValue(array $keys, array $data)
    {
        $lastKey = end($keys);

        foreach (array_values($keys) as $key) {
            if (!isset($data[$key])) {
                return false;
            }
            $data = $data[$key];

            if (
                (!is_array($data) && is_scalar($data))
                || $lastKey === $key
            ) {
                return $data;
            }
        }

        return false;
    }
}
