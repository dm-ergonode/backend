<?php
/*
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\ExporterShopware6\Infrastructure\Mapper;

use Ergonode\Attribute\Domain\Entity\AbstractOption;
use Ergonode\Core\Domain\ValueObject\Language;
use Ergonode\ExporterShopware6\Domain\Entity\Shopware6Channel;
use Ergonode\ExporterShopware6\Infrastructure\Model\Shopware6PropertyGroupOption;

interface Shopware6PropertyGroupOptionMapperInterface
{
    public function map(
        Shopware6Channel $channel,
        Shopware6PropertyGroupOption $propertyGroupOption,
        AbstractOption $option,
        ?Language $language = null
    ): Shopware6PropertyGroupOption;
}
