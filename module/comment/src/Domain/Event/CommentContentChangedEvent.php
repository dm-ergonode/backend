<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Comment\Domain\Event;

use Ergonode\EventSourcing\Infrastructure\DomainEventInterface;
use Ergonode\SharedKernel\Domain\Aggregate\CommentId;
use Ergonode\SharedKernel\Domain\AggregateId;
use JMS\Serializer\Annotation as JMS;

class CommentContentChangedEvent implements DomainEventInterface
{
    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\CommentId")
     */
    private CommentId $id;

    /**
     * @JMS\Type("string")
     */
    private string $from;

    /**
     * @JMS\Type("string")
     */
    private string $to;

    /**
     * @JMS\Type("DateTime")
     */
    private \DateTime $editedAt;

    public function __construct(CommentId $id, string $from, string $to, \DateTime $editedAt)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->editedAt = $editedAt;
    }

    /**
     * @return CommentId|AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->id;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getEditedAt(): \DateTime
    {
        return $this->editedAt;
    }
}
