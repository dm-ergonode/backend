<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Editor\Domain\Command;

use Ergonode\SharedKernel\Domain\Aggregate\AttributeId;
use Ergonode\Core\Domain\ValueObject\Language;
use Ergonode\SharedKernel\Domain\Aggregate\ProductDraftId;
use Ergonode\EventSourcing\Infrastructure\DomainCommandInterface;
use JMS\Serializer\Annotation as JMS;

class ChangeProductAttributeValueCommand implements DomainCommandInterface
{
    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\ProductDraftId")
     */
    private ProductDraftId $id;

    /**
     * @JMS\Type("Ergonode\SharedKernel\Domain\Aggregate\AttributeId")
     */
    private AttributeId $attributeId;

    /**
     * @JMS\Type("Ergonode\Core\Domain\ValueObject\Language")
     */
    private Language $language;

    /**
     * @var string|array|null
     *
     * @JMS\Type("string")
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(ProductDraftId $id, AttributeId $attributeId, Language $language, $value = null)
    {
        $this->id = $id;
        $this->attributeId = $attributeId;
        $this->language = $language;
        $this->value = $value;
    }

    public function getId(): ProductDraftId
    {
        return $this->id;
    }

    public function getAttributeId(): AttributeId
    {
        return $this->attributeId;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return string|array|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
