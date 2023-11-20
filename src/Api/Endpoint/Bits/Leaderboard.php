<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Bits;

use ArrayIterator;
use Countable;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

class Leaderboard implements IteratorAggregate, Countable
{
    public function __construct(
        public array              $leaders,
        public int                $total,
        public ?DateTimeInterface $startedAt = null,
        public ?DateTimeInterface $endedAt = null
    )
    {
    }

    /**
     * @return Traversable<Leader>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->leaders);
    }

    public function count(): int
    {
        return $this->total;
    }
}
