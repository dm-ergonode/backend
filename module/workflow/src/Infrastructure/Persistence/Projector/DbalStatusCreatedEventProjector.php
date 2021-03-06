<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Workflow\Infrastructure\Persistence\Projector;

use Doctrine\DBAL\Connection;
use Ergonode\Workflow\Domain\Event\Status\StatusCreatedEvent;
use JMS\Serializer\SerializerInterface;

class DbalStatusCreatedEventProjector
{
    private const TABLE = 'status';

    private Connection $connection;

    private SerializerInterface $serializer;

    public function __construct(Connection $connection, SerializerInterface $serializer)
    {
        $this->connection = $connection;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(StatusCreatedEvent $event): void
    {
        $this->connection->insert(
            self::TABLE,
            [
                'id' => $event->getAggregateId()->getValue(),
                'code' => $event->getCode(),
                'name' => $this->serializer->serialize($event->getName()->getTranslations(), 'json'),
                'description' => $this->serializer->serialize($event->getDescription()->getTranslations(), 'json'),
                'color' => $event->getColor()->getValue(),
            ]
        );
    }
}
