<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\MockHttpClient;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\Leaderboard;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\Endpoint\Pagination;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\Subscription;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations;
use TBoileau\TwitchApi\Api\TwitchApi;
use TBoileau\TwitchApi\Test\TwitchApiTestCase;

class TwitchApiTest extends TwitchApiTestCase
{
    public function setUp(): void
    {
        $this->createApi();
    }

    #[Test]
    public function shouldThrowExceptionIfOperationDoesNotExist(): void
    {
        $twitchApi = new TwitchApi([], new MockHttpClient());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The operation "Foo" does not exist.');
        $twitchApi->Foo;
    }

    #[Test]
    public function shouldThrowExceptionIfOperationIsNotAnInstanceOfAbstractOperations(): void
    {
        $twitchApi = new TwitchApi(['Foo' => new \stdClass()], new MockHttpClient());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The operation "Foo" is not an instance of "%s".', AbstractOperations::class));
        $twitchApi->Foo;
    }

    /**
     * @param class-string<AbstractOperations> $operationsClass
     */
    #[Test]
    #[DataProvider('provideGroupsOperations')]
    public function shouldReturnOperations(string $operationsClass, string $operationName): void
    {
        $this->assertInstanceOf($operationsClass, $this->twitchApi->$operationName);
    }

    /**
     * @return \Generator<array{class-string<AbstractOperations>, string}>
     */
    public static function provideGroupsOperations(): \Generator
    {
        yield [ChannelOperations::class, 'Channel'];
        yield [BitsOperations::class, 'Bits'];
        yield [SubscriptionsOperations::class, 'Subscriptions'];
    }

    #[Test]
    #[DataProvider('provideOperations')]
    public function shouldCallOperation(\Closure $operation): void
    {
        $operation->call($this);
    }

    public static function provideOperations(): \Generator
    {
        yield 'Get Bits Leaderboard' => [function (): void {
            $leaderboard = $this->call('Bits::getLeaderboard');
            self::assertInstanceOf(Leaderboard::class, $leaderboard);
        }];
        yield 'Get Broadcaster Subscriptions' => [function (): void {
            $pagination = $this->call('Subscriptions::getBroadcasterSubscriptions', [$this->userId]);
            self::assertInstanceOf(Pagination::class, $pagination);
            self::assertContainsOnlyInstancesOf(Subscription::class, $pagination->getIterator());
        }];
    }
}
