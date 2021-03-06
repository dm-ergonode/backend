<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Workflow\Application\Form\Model\Workflow;

use Symfony\Component\Validator\Constraints as Assert;
use Ergonode\Workflow\Infrastructure\Validator\StatusIdNotExists;
use Ergonode\Workflow\Infrastructure\Validator\WorkflowExists;

class WorkflowFormModel
{
    /**
     * @Assert\NotBlank(groups={"Create"})
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Workflow name is too long. It should contain {{ limit }} characters or less.",
     *     groups={"Create"}
     *     )
     * @WorkflowExists(groups={"Create"})
     */
    public ?string $code = null;

    /**
     * @var string[]
     *
     * @Assert\All({
     *     @Assert\NotBlank(),
     *
     *     @StatusIdNotExists(),
     * })
     */
    public array $statuses = [];
}
