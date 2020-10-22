<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Grid\Filter;

use Ergonode\Grid\FilterInterface;

class HasFilter implements FilterInterface
{
    public const TYPE = 'HAS';

    /**
     * @return array
     */
    public function render(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE;
    }
}
