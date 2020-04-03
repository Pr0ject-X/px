<?php

declare(strict_types=1);

namespace Pr0jectX\Px\State;

/**
 * Define the state interface.
 */
interface StateInterface
{
    /**
     * Retrieve a value from the state data.
     *
     * @param null $key
     *   A key or an array of keys within the data.
     *
     * @return mixed
     *   The retrieved value from the data state.
     */
    public function get($key = null);

    /**
     * Delete a key/value from the state data.
     *
     * @param string $key
     *   The state data key.
     */
    public function del(string $key);

    /**
     * Set a value in the state data.
     *
     * @param string $key
     *   The state data key.
     * @param $value
     *   The value to set in the state.
     */
    public function set(string $key, $value);

    /**
     * Save the state data.
     *
     * @return bool
     *   Return true if data was saved; otherwise false.
     */
    public function save(): bool;
}
