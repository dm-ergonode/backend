<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Condition\Domain\Condition;

use Ergonode\Condition\Domain\ConditionInterface;
use Ergonode\SharedKernel\Domain\Aggregate\StatusId;
use JMS\Serializer\Annotation as JMS;

class ProductHasStatusCondition implements ConditionInterface
{
    public const TYPE = 'PRODUCT_HAS_STATUS_CONDITION';
    public const PHRASE = 'PRODUCT_HAS_STATUS_CONDITION_PHRASE';

    public const HAS = 'HAS';
    public const NOT_HAS = 'NOT_HAS';

    /**
     * @JMS\Type("string")
     */
    private string $operator;

    /**
     * @var StatusId[]
     *
     * @JMS\Type("array<Ergonode\SharedKernel\Domain\Aggregate\StatusId>")
     */
    private array $value;

    /**
     * @param StatusId[] $value
     */
    public function __construct(string $operator, array $value)
    {
        $this->operator = $operator;
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

    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return StatusId[]
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public static function getSupportedOperators(): array
    {
        return
            [
                self::HAS,
                self::NOT_HAS,
            ];
    }
}
