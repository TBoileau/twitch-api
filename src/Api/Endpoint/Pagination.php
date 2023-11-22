<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 */
class Pagination implements IteratorAggregate, Countable
{
    /**
     * @param T[] $data
     */
    public function __construct(
        private array     $data,
        private int       $total,
        private ?Closure $next = null,
        private ?Closure $previous = null
    ) {
    }

    /**
     * @return Traversable<T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function count(): int
    {
        return $this->total;
    }

    public function next(): ?static
    {
        if ($this->next === null) {
            return null;
        }

        return ($this->next)();
    }

    public function previous()
    {
        if ($this->previous === null) {
            return null;
        }

        return ($this->previous)();
    }
}
