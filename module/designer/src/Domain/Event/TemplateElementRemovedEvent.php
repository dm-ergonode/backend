<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Designer\Domain\Event;

use Ergonode\Designer\Domain\ValueObject\Position;
use Ergonode\EventSourcing\Infrastructure\DomainEventInterface;
use Ergonode\SharedKernel\Domain\Aggregate\TemplateId;
use JMS\Serializer\Annotation as JMS;

class TemplateElementRemovedEvent implements DomainEventInterface
{
    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\TemplateId")
     */
    private TemplateId $id;

    /**
     * @JMS\Type("Ergonode\Designer\Domain\ValueObject\Position")
     */
    private Position $position;

    public function __construct(TemplateId $id, Position $position)
    {
        $this->id = $id;
        $this->position = $position;
    }

    public function getAggregateId(): TemplateId
    {
        return $this->id;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }
}
