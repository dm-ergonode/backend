<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Transformer\Infrastructure\Converter\Mapper;

use Ergonode\Transformer\Infrastructure\Converter\ConverterInterface;
use Ergonode\Transformer\Infrastructure\Converter\JoinConverter;

class JoinConverterMapper implements ConverterMapperInterface
{
    public function supported(ConverterInterface $converter): bool
    {
        return $converter instanceof JoinConverter;
    }

    /**
     * @param ConverterInterface|JoinConverter $converter
     * @param array                            $line
     */
    public function map(ConverterInterface $converter, array $line, ?string $default = null): ?string
    {
        $fields = [];
        foreach ($line as $key => $value) {
            $fields[sprintf('<%s>', $key)] = $value;
        }

        return str_replace(array_keys($fields), $fields, $converter->getPattern());
    }
}
