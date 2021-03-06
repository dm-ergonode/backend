<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Product\Application\Model\Product\Relation;

use Ergonode\Product\Infrastructure\Validator\ProductNoBindings;
use Symfony\Component\Validator\Constraints as Assert;
use Ergonode\Product\Infrastructure\Validator\ProductExists;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;
use Ergonode\Product\Infrastructure\Validator\ProductChild;
use Ergonode\Product\Infrastructure\Validator\ProductType;

/**
 * @ProductChild
 */
class ProductChildFormModel
{
    /**
     * @ProductType(type={"VARIABLE-PRODUCT", "GROUPING-PRODUCT"})
     *
     * @ProductNoBindings(groups={"VARIABLE-PRODUCT"})
     */
    private ProductId $parentId;

    /**
     * @Assert\NotBlank(message="Child product is required")
     * @Assert\Uuid(strict=true)
     *
     * @ProductExists()
     *
     * @ProductType(type={"SIMPLE-PRODUCT"})
     */
    public ?string $childId = null;

    public function __construct(ProductId $parentId)
    {
        $this->parentId = $parentId;
    }

    public function getParentId(): ProductId
    {
        return $this->parentId;
    }
}
