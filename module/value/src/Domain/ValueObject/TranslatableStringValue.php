<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Value\Domain\ValueObject;

use Ergonode\Core\Domain\ValueObject\TranslatableString;
use JMS\Serializer\Annotation as JMS;
use Ergonode\Core\Domain\ValueObject\Language;

class TranslatableStringValue implements ValueInterface
{
    public const TYPE = 'translation';

    /**
     * @JMS\Type("Ergonode\Core\Domain\ValueObject\TranslatableString")
     */
    private TranslatableString $value;

    public function __construct(TranslatableString $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     *
     * @JMS\VirtualProperty()
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value->getTranslations();
    }

    public function getTranslation(Language $language): ?string
    {
        return $this->value->get($language);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return implode(',', $this->value->getTranslations());
    }

    public function isEqual(ValueInterface $value): bool
    {
        return
            $value instanceof self
            && $value->value->isEqual($this->value);
    }
}
