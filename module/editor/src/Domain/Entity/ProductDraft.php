<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Editor\Domain\Entity;

use Ergonode\Attribute\Domain\ValueObject\AttributeCode;
use Ergonode\Editor\Domain\Event\ProductDraftApplied;
use Ergonode\Editor\Domain\Event\ProductDraftCreated;
use Ergonode\Editor\Domain\Event\ProductDraftValueAdded;
use Ergonode\Editor\Domain\Event\ProductDraftValueChanged;
use Ergonode\Editor\Domain\Event\ProductDraftValueRemoved;
use Ergonode\EventSourcing\Domain\AbstractAggregateRoot;
use Ergonode\Product\Domain\Entity\AbstractProduct;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;
use Ergonode\SharedKernel\Domain\Aggregate\ProductDraftId;
use Ergonode\Value\Domain\ValueObject\ValueInterface;
use JMS\Serializer\Annotation as JMS;

class ProductDraft extends AbstractAggregateRoot
{
    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\ProductDraftId")
     */
    private ProductDraftId $id;

    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\ProductId")
     */
    private ProductId $productId;

    /**
     * @JMS\Type("bool")
     */
    private bool $applied;

    /**
     * @var ValueInterface[]
     *
     * @JMS\Type("array<string, Ergonode\Value\Domain\ValueObject\ValueInterface>")
     */
    private array $attributes;

    public function __construct(ProductDraftId $id, AbstractProduct $product)
    {
        $this->apply(new ProductDraftCreated($id, $product->getId()));

        foreach ($product->getAttributes() as $attributeCode => $value) {
            $this->apply(new ProductDraftValueAdded($this->id, new AttributeCode((string) $attributeCode), $value));
        }
    }

    public function getId(): ProductDraftId
    {
        return $this->id;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function isApplied(): bool
    {
        return $this->applied;
    }

    public function applied(): void
    {
        $this->apply(new ProductDraftApplied($this->id));
    }

    public function hasAttribute(AttributeCode $attributeCode): bool
    {
        return isset($this->attributes[$attributeCode->getValue()]);
    }


    public function getAttribute(AttributeCode $attributeCode): ValueInterface
    {
        if (!$this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value not exists');
        }

        return clone $this->attributes[$attributeCode->getValue()];
    }

    /**
     * @return ValueInterface[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(AttributeCode $attributeCode, ValueInterface $valueInterface): void
    {
        if ($this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value already exists');
        }

        $this->apply(new ProductDraftValueAdded($this->id, $attributeCode, $valueInterface));
    }

    public function changeAttribute(AttributeCode $attributeCode, ValueInterface $new): void
    {
        if (!$this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value note exists');
        }

        $old = $this->attributes[$attributeCode->getValue()];

        if (!$new->isEqual($old)) {
            $this->apply(new ProductDraftValueChanged($this->id, $attributeCode, $old, $new));
        }
    }

    public function removeAttribute(AttributeCode $attributeCode): void
    {
        if (!$this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value note exists');
        }

        $this
            ->apply(
                new ProductDraftValueRemoved(
                    $this->id,
                    $attributeCode,
                    $this->attributes[$attributeCode->getValue()]
                )
            );
    }

    protected function applyProductDraftCreated(ProductDraftCreated $event): void
    {
        $this->id = $event->getAggregateId();
        $this->productId = $event->getProductId();
        $this->attributes = [];
        $this->applied = false;
    }

    protected function applyProductDraftValueAdded(ProductDraftValueAdded $event): void
    {
        $this->attributes[$event->getAttributeCode()->getValue()] = $event->getTo();
    }

    protected function applyProductDraftValueChanged(ProductDraftValueChanged $event): void
    {
        $this->attributes[$event->getAttributeCode()->getValue()] = $event->getTo();
    }

    protected function applyProductDraftValueRemoved(ProductDraftValueRemoved $event): void
    {
        unset($this->attributes[$event->getAttributeCode()->getValue()]);
    }

    protected function applyProductDraftApplied(ProductDraftApplied $event): void
    {
        $this->applied = true;
    }
}
