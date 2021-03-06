<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Product\Application\Request\ParamConverter;

use Ergonode\Product\Domain\Entity\AbstractProduct;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;
use Ergonode\Product\Domain\Repository\ProductRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractProductParamConverter implements ParamConverterInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration): void
    {
        if ($configuration->getName()) {
            $parameter = $request->get($configuration->getName());
        } else {
            $parameter = $request->get('product');
        }

        if (null === $parameter) {
            throw new BadRequestHttpException('Request parameter "product" is missing');
        }

        if (!ProductId::isValid($parameter)) {
            throw new BadRequestHttpException('Invalid product ID');
        }

        $entity = $this->productRepository->load(new ProductId($parameter));

        $class = $configuration->getClass();
        if (!$entity instanceof $class) {
            throw new BadRequestHttpException("Wrong url argument");
        }

        if (null === $entity) {
            throw new NotFoundHttpException(sprintf('Product by ID "%s" not found', $parameter));
        }

        $request->attributes->set($configuration->getName(), $entity);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return is_subclass_of($configuration->getClass(), AbstractProduct::class) ||
            $configuration->getClass() === AbstractProduct::class;
    }
}
