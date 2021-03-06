<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Exporter\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ergonode\Exporter\Domain\Entity\Export;
use Ergonode\Exporter\Domain\Repository\ExportRepositoryInterface;
use Ergonode\Exporter\Infrastructure\Persistence\Repository\Factory\DbalExportFactory;
use Ergonode\Exporter\Infrastructure\Persistence\Repository\Mapper\DbalExportMapper;
use Ergonode\SharedKernel\Domain\Aggregate\ExportId;

class DbalExportRepository implements ExportRepositoryInterface
{
    private const TABLE = 'exporter.export';
    private const FIELDS = [
        'id',
        'status',
        'items',
        'channel_id',
        'started_at',
        'ended_at',
    ];

    private Connection $connection;

    private DbalExportFactory $factory;

    private DbalExportMapper $mapper;

    public function __construct(Connection $connection, DbalExportFactory $factory, DbalExportMapper $mapper)
    {
        $this->connection = $connection;
        $this->factory = $factory;
        $this->mapper = $mapper;
    }

    /**
     * @throws \ReflectionException
     */
    public function load(ExportId $id): ?Export
    {
        $qb = $this->getQuery();
        $record = $qb->where($qb->expr()->eq('id', ':id'))
            ->setParameter(':id', $id->getValue())
            ->execute()
            ->fetch();

        if ($record) {
            return $this->factory->create($record);
        }

        return null;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save(Export $export): void
    {
        if ($this->exists($export->getId())) {
            $this->update($export);
        } else {
            $this->insert($export);
        }
    }

    public function exists(ExportId $id): bool
    {
        $query = $this->connection->createQueryBuilder();
        $result = $query->select(1)
            ->from(self::TABLE)
            ->where($query->expr()->eq('id', ':id'))
            ->setParameter(':id', $id->getValue())
            ->execute()
            ->rowCount();

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function update(Export $export): void
    {
        $exportArray = $this->mapper->map($export);
        $exportArray['updated_at'] = new \DateTime();

        $this->connection->update(
            self::TABLE,
            $exportArray,
            [
                'id' => $export->getId()->getValue(),
            ],
            [
                'started_at' => Types::DATETIMETZ_MUTABLE,
                'ended_at' => Types::DATETIMETZ_MUTABLE,
                'updated_at' => Types::DATETIMETZ_MUTABLE,
            ],
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function insert(Export $export): void
    {
        $exportArray = $this->mapper->map($export);
        $exportArray['created_at'] = $exportArray['updated_at'] = new \DateTime();

        $this->connection->insert(
            self::TABLE,
            $exportArray,
            [
                'started_at' => Types::DATETIMETZ_MUTABLE,
                'ended_at' => Types::DATETIMETZ_MUTABLE,
                'created_at' => Types::DATETIMETZ_MUTABLE,
                'updated_at' => Types::DATETIMETZ_MUTABLE,
            ],
        );
    }

    private function getQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select(self::FIELDS)
            ->from(self::TABLE);
    }
}
