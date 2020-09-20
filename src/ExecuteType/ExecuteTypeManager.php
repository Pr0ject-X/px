<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecuteType;

use Pr0jectX\Px\Contracts\ExecuteTypeInterface;
use Robo\Collection\CollectionBuilder;
use Robo\Result;

/**
 * Define the execute type manager class.
 */
class ExecuteTypeManager
{
    /**
     * Create an execute type instance.
     *
     * @param string $name
     *   The execute type name.
     * @param array $values
     *   The execute type values.
     *
     * @return \Pr0jectX\Px\Contracts\ExecuteTypeInterface
     *   A fully instantiated execute type instance.
     */
    public function createInstance(
        string $name,
        array $values
    ): ExecuteTypeInterface {
        $registeredTypes = $this->registeredTypes();

        if (!isset($registeredTypes[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" execute type is invalid!',
                $name
            ));
        }

        return new $registeredTypes[$name]($values);
    }

    /**
     * Execute the execute type instances.
     *
     * @param array $executeTypes
     *   An array of execute types.
     * @param \Robo\Collection\CollectionBuilder $collection
     *   The collection builder service.
     *
     * @return \Robo\Result
     */
    public function executeInstances(
        array $executeTypes,
        CollectionBuilder $collection
    ): Result {
        /** @var \Pr0jectX\Px\Contracts\ExecuteTypeInterface $executeType */
        foreach ($executeTypes as $executeType) {
            if (!$executeType instanceof ExecuteTypeInterface) {
                continue;
            }
            $collection->addTask(
                $executeType->createTaskInstance()
            );
        }

        return $collection->run();
    }

    /**
     * Register the available execute types.
     *
     * @return array
     *   An array of available execute types.
     */
    protected function registeredTypes(): array
    {
        return [
            'shell' => ExecuteShellType::class,
            'symfony' => ExecuteSymfonyType::class,
        ];
    }
}
