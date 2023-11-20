<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api\Endpoint;

use PHPUnit\Framework\TestCase;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;

class ChannelOperationsTest extends TestCase
{
    public function testGetFollowers(): void
    {
        $channelOperations = new ChannelOperations();

        $pagination = $channelOperations->getFollowers('123', 20);

        self::assertCount(30, $pagination);
        self::assertCount(20, $pagination->getIterator());
        self::assertContainsOnlyInstancesOf(Follower::class, $pagination->getIterator());
        $pagination = $pagination->next();

        self::assertCount(10, $pagination->getIterator());
    }
}
