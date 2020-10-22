<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Product\Infrastructure\Handler\Category;

use Ergonode\Product\Domain\Command\Category\AddProductCategoryCommand;
use Ergonode\Product\Domain\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

class AddProductCategoryCommandHandler
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param AddProductCategoryCommand $command
     *
     * @throws \Exception
     */
    public function __invoke(AddProductCategoryCommand $command)
    {
        $product = $this->productRepository->load($command->getId());
        Assert::notNull($product);

        $product->addToCategory($command->getCategoryId());

        $this->productRepository->save($product);
    }
}
