<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Product\Application\Provider;

interface ProductSupportProviderInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool;
}
