<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Category\Application\Form\Transformer;

use Ergonode\Category\Domain\ValueObject\CategoryCode;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CategoryCodeDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($value): ?string
    {
        if ($value) {
            if ($value instanceof CategoryCode) {
                return $value->getValue();
            }

            throw new TransformationFailedException('Invalid CategoryCode object');
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $value
     */
    public function reverseTransform($value): ?CategoryCode
    {
        if ($value) {
            try {
                return new CategoryCode($value);
            } catch (\InvalidArgumentException $e) {
                throw new TransformationFailedException(sprintf('Invalid category code %s value', $value));
            }
        }

        return null;
    }
}
