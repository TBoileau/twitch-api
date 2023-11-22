<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use InvalidArgumentException;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;

class TwitchApi implements TwitchApiInterface
{
    /**
     * @var array<string, AbstractOperations>
     */
    private array $operations;

    /**
     * @param iterable<string, AbstractOperations> $operations
     */
    public function __construct(iterable $operations = [])
    {
        $this->operations = iterator_to_array($operations);
    }

    public function __get(string $name): AbstractOperations
    {
        if (!isset($this->operations[$name])) {
            throw new InvalidArgumentException(sprintf('The operation "%s" does not exist.', $name));
        }

        if (!$this->operations[$name] instanceof AbstractOperations) {
            throw new InvalidArgumentException(sprintf('The operation "%s" is not an instance of "%s".', $name, AbstractOperations::class));
        }

        return $this->operations[$name];
    }
}
