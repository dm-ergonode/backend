<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Attribute\Infrastructure\Persistence\Projector\Attribute;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Ergonode\Attribute\Domain\Event\Attribute\AttributeLabelChangedEvent;
use Ergonode\SharedKernel\Domain\Aggregate\AttributeId;
use Ramsey\Uuid\Uuid;

class DbalAttributeLabelChangedEventProjector
{
    private const TABLE = 'value_translation';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws DBALException
     */
    public function __invoke(AttributeLabelChangedEvent $event): void
    {
        $from = $event->getFrom()->getTranslations();
        $to = $event->getTo()->getTranslations();
        $aggregateId = $event->getAggregateId();

        foreach ($to as $language => $value) {
            $result = $this->connection->update(
                self::TABLE,
                [
                    'language' => $language,
                    'value' => $value,
                ],
                [
                    'value_id' => $this->getTranslationId('label', $aggregateId),
                    'language' => $language,
                ]
            );
            if (!$result) {
                $this->connection->insert(
                    self::TABLE,
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'value_id' => $this->getTranslationId('label', $aggregateId),
                        'language' => $language,
                        'value' => $value,
                    ]
                );
            }
        }

        foreach (array_keys($from) as $language) {
            if (!isset($to[$language])) {
                $this->connection->delete(
                    self::TABLE,
                    [
                        'value_id' => $this->getTranslationId('label', $aggregateId),
                        'language' => $language,
                    ]
                );
            }
        }
    }

    private function getTranslationId(string $field, AttributeId $attributeId): string
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select($field)
            ->from('attribute')
            ->where($qb->expr()->eq('id', ':id'))
            ->setParameter(':id', $attributeId->getValue())
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);
    }
}
