<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Exporter\Domain\Repository;

use Ergonode\Exporter\Domain\Entity\Export;
use Ergonode\SharedKernel\Domain\Aggregate\ExportId;

interface ExportRepositoryInterface
{
    public function load(ExportId $id): ?Export;

    public function save(Export $export): void;

    public function exists(ExportId $id): bool;
}
