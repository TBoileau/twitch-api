<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use Exception;
use Traversable;

/**
 * @template T
 */
class Pagination implements \IteratorAggregate, \Countable
{
    /**
     * @param T[] $data
     */
    public function __construct(
        private array $data,
        private int $total,
        private \Closure $next
    ) {
    }

    /**
     * @return Traversable<T>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return $this->total;
    }

    public function next(): static
    {
        return ($this->next)();
    }
}
