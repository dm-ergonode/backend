<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Importer\Domain\Command\Import;

use Ergonode\EventSourcing\Infrastructure\DomainCommandInterface;
use Ergonode\SharedKernel\Domain\Aggregate\ImportId;
use JMS\Serializer\Annotation as JMS;

class EndImportCommand implements DomainCommandInterface
{
    /**
     * @JMS\Type("Ergonode\Transformer\Domain\Entity\ImportId")
     */
    private ImportId $id;

    public function __construct(ImportId $id)
    {
        $this->id = $id;
    }

    public function getId(): ImportId
    {
        return $this->id;
    }
}
