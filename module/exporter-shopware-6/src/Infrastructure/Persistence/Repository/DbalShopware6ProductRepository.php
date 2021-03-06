<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\ExporterShopware6\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Types;
use Ergonode\ExporterShopware6\Domain\Repository\Shopware6ProductRepositoryInterface;
use Ergonode\SharedKernel\Domain\Aggregate\ChannelId;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;

class DbalShopware6ProductRepository implements Shopware6ProductRepositoryInterface
{
    private const TABLE = 'exporter.shopware6_product';
    private const FIELDS = [
        'channel_id',
        'product_id',
        'shopware6_id',
    ];

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function load(ChannelId $channelId, ProductId $productId): ?string
    {
        $query = $this->connection->createQueryBuilder();
        $record = $query
            ->select(self::FIELDS)
            ->from(self::TABLE, 'p')
            ->where($query->expr()->eq('p.channel_id', ':channelId'))
            ->setParameter(':channelId', $channelId->getValue())
            ->andWhere($query->expr()->eq('p.product_id', ':productId'))
            ->setParameter(':productId', $productId->getValue())
            ->execute()
            ->fetch();

        if ($record) {
            return $record['shopware6_id'];
        }

        return null;
    }

    /**
     * @throws DBALException
     */
    public function save(ChannelId $channelId, ProductId $productId, string $shopwareId): void
    {
        if ($this->exists($channelId, $productId)) {
            $this->update($channelId, $productId, $shopwareId);
        } else {
            $this->insert($channelId, $productId, $shopwareId);
        }
    }

    public function exists(ChannelId $channelId, ProductId $productId): bool
    {
        $query = $this->connection->createQueryBuilder();
        $result = $query->select(1)
            ->from(self::TABLE, 'p')
            ->where($query->expr()->eq('p.channel_id', ':channelId'))
            ->setParameter(':channelId', $channelId->getValue())
            ->andWhere($query->expr()->eq('p.product_id', ':productId'))
            ->setParameter(':productId', $productId->getValue())
            ->execute()
            ->rowCount();


        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * @throws DBALException
     */
    private function update(ChannelId $channelId, ProductId $productId, string $shopwareId): void
    {
        $this->connection->update(
            self::TABLE,
            [
                'shopware6_id' => $shopwareId,
                'update_at' => new \DateTimeImmutable(),
            ],
            [
                'product_id' => $productId->getValue(),
                'channel_id' => $channelId->getValue(),
            ],
            [
                'update_at' => Types::DATETIMETZ_MUTABLE,
            ],
        );
    }

    /**
     * @throws DBALException
     */
    private function insert(ChannelId $channelId, ProductId $productId, string $shopwareId): void
    {
        $this->connection->insert(
            self::TABLE,
            [
                'shopware6_id' => $shopwareId,
                'product_id' => $productId->getValue(),
                'channel_id' => $channelId->getValue(),
                'update_at' => new \DateTimeImmutable(),
            ],
            [
                'update_at' => Types::DATETIMETZ_MUTABLE,
            ],
        );
    }
}
