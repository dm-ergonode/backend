<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Completeness\Domain\Command;

use Ergonode\SharedKernel\Domain\Aggregate\ProductId;
use Ergonode\EventSourcing\Infrastructure\DomainCommandInterface;

class ProductCompletenessCalculateCommand implements DomainCommandInterface
{
    private ProductId $productId;

    public function __construct(ProductId $productId)
    {
        $this->productId = $productId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
