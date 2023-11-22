<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Bits;

use DateTimeImmutable;
use DateTimeInterface;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;

class BitsOperations extends AbstractOperations
{
    public static function getName(): string
    {
        return 'Bits';
    }

    public static function getScopes(): array
    {
        return ['bits:read'];
    }

    public function getLeaderboard(
        ?int               $count = null,
        ?string            $period = null,
        ?DateTimeInterface $startedAt = null,
        ?string            $userId = null,
    ): Leaderboard {
        /** @var array{data: array<array-key, array{user_id: string, user_login: string, user_name: string, rank: int, score: int}>, date_range?: array{started_at: string, ended_at: string}, total: int} $response */
        $response = $this->httpClient->request('GET', 'bits/leaderboard', [
            'query' => [
                'count' => $count,
                'period' => $period,
                'started_at' => $startedAt === null ? null : $startedAt->format('U'),
                'user_id' => $userId,
            ],
        ])->toArray();

        return new Leaderboard(
            array_map(
                fn(array $leader): Leader => new Leader(
                    $leader['user_id'],
                    $leader['user_login'],
                    $leader['user_name'],
                    $leader['rank'],
                    $leader['score'],
                ),
                $response['data']
            ),
            $response['total'],
            isset($response['date_range']) ? new DateTimeImmutable($response['date_range']['started_at']) : null,
            isset($response['date_range']) ? new DateTimeImmutable($response['date_range']['ended_at']) : null,
        );
    }
}
