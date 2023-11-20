<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;

class TwitchApi implements TwitchApiInterface
{
    /**
     * @param array<string, AbstractOperations> $operations
     */
    public function __construct(
        private iterable $operations = []
    ) {
    }

    public function __get(string $name): AbstractOperations
    {
        if (!isset($this->operations[$name])) {
            throw new \InvalidArgumentException(sprintf('The operation "%s" does not exist.', $name));
        }

        if (!$this->operations[$name] instanceof AbstractOperations) {
            throw new \InvalidArgumentException(sprintf('The operation "%s" is not an instance of "%s".', $name, AbstractOperations::class));
        }

        return $this->operations[$name];
    }
}
