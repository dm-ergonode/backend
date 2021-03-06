<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Workflow\Domain\Command\Workflow;

use Ergonode\EventSourcing\Infrastructure\DomainCommandInterface;
use Ergonode\SharedKernel\Domain\Aggregate\WorkflowId;

class DeleteWorkflowCommand implements DomainCommandInterface
{
    private WorkflowId $id;

    public function __construct(WorkflowId $id)
    {
        $this->id = $id;
    }

    public function getId(): WorkflowId
    {
        return $this->id;
    }
}
