<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Product\Infrastructure\Factory\Command;

use Symfony\Component\Form\FormInterface;
use Ergonode\EventSourcing\Infrastructure\DomainCommandInterface;

interface CreateProductCommandFactoryInterface
{
    public function support(string $type): bool;

    /**
     * @throws \Exception
     */
    public function create(FormInterface $form): DomainCommandInterface;
}
