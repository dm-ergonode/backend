<?php
/*
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\ExporterShopware6\Infrastructure\Processor\Step;

use Ergonode\EventSourcing\Infrastructure\Bus\CommandBusInterface;
use Ergonode\ExporterShopware6\Domain\Command\Export\PropertyGroupShopware6ExportCommand;
use Ergonode\ExporterShopware6\Domain\Entity\Shopware6Channel;
use Ergonode\ExporterShopware6\Infrastructure\Processor\Shopware6ExportStepProcessInterface;
use Ergonode\Product\Domain\Entity\VariableProduct;
use Ergonode\Product\Domain\Query\ProductQueryInterface;
use Ergonode\Product\Domain\Repository\ProductRepositoryInterface;
use Ergonode\Segment\Domain\Query\SegmentProductsQueryInterface;
use Ergonode\SharedKernel\Domain\Aggregate\AttributeId;
use Ergonode\SharedKernel\Domain\Aggregate\ExportId;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;
use Webmozart\Assert\Assert;

class Shopware6PropertyGroupStep implements Shopware6ExportStepProcessInterface
{
    private ProductQueryInterface $productQuery;

    private SegmentProductsQueryInterface  $segmentProductsQuery;

    private ProductRepositoryInterface $productRepository;

    private CommandBusInterface $commandBus;

    public function __construct(
        ProductQueryInterface $productQuery,
        SegmentProductsQueryInterface $segmentProductsQuery,
        ProductRepositoryInterface $productRepository,
        CommandBusInterface $commandBus
    ) {
        $this->productQuery = $productQuery;
        $this->segmentProductsQuery = $segmentProductsQuery;
        $this->productRepository = $productRepository;
        $this->commandBus = $commandBus;
    }

    public function export(ExportId $exportId, Shopware6Channel $channel): void
    {
        $attributeList = array_unique(array_merge($this->getBindingAttributes($channel), $channel->getPropertyGroup()));

        foreach ($attributeList as $attributeId) {
            $processCommand = new PropertyGroupShopware6ExportCommand($exportId, $attributeId);
            $this->commandBus->dispatch($processCommand, true);
        }
    }

    /**
     * @return AttributeId[]
     */
    private function getBindingAttributes(Shopware6Channel $channel): array
    {
        $attribute = [];
        $products = $this->getProduct($channel);
        foreach ($products as $product) {
            $productId = new ProductId($product);
            $domainProduct = $this->productRepository->load($productId);
            Assert::isInstanceOf($domainProduct, VariableProduct::class);
            $bindings = $domainProduct->getBindings();
            $attribute = array_unique(array_merge($attribute, $bindings));
        }

        return $attribute;
    }

    /**
     * @return array
     */
    private function getProduct(Shopware6Channel $channel): array
    {
        if ($channel->getSegment()) {
            return $this->segmentProductsQuery->getProductsByType($channel->getSegment(), VariableProduct::TYPE);
        }

        return $this->productQuery->findProductIdByType(VariableProduct::TYPE);
    }
}
