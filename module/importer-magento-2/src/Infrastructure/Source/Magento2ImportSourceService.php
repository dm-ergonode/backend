<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\ImporterMagento2\Infrastructure\Source;

use Ergonode\Importer\Infrastructure\Provider\ImportSourceInterface;

class Magento2ImportSourceService implements ImportSourceInterface
{
    public const TYPE = 'magento-2-csv';

    public static function getType(): string
    {
        return self::TYPE;
    }

    public function supported(string $type): bool
    {
        return self::TYPE === $type;
    }
}
