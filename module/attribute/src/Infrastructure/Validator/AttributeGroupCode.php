<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Attribute\Infrastructure\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AttributeGroupCode extends Constraint
{
    public string $validMessage = 'Given value {{ value }} is not valid attribute group code';

    public string $uniqueMessage = 'The value is not unique.';
}
