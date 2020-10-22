<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Generator\Builder\Domain\Factory;

use Ergonode\Generator\Builder\BuilderInterface;
use Ergonode\Generator\Builder\FileBuilder;
use Ergonode\Generator\Builder\MethodBuilder;
use Nette\PhpGenerator\PhpFile;

class EntityFactoryInterfaceBuilder implements BuilderInterface
{
    /**
     * @var FileBuilder
     */
    private FileBuilder $builder;

    /**
     * @var MethodBuilder
     */
    private MethodBuilder $methodBuilder;

    /**
     * @param FileBuilder   $builder
     * @param MethodBuilder $methodBuilder
     */
    public function __construct(FileBuilder $builder, MethodBuilder $methodBuilder)
    {
        $this->builder = $builder;
        $this->methodBuilder = $methodBuilder;
    }

    /**
     * @param string $module
     * @param string $entity
     *
     * @param array  $properties
     *
     * @return PhpFile
     */
    public function build(string $module, string $entity, array $properties = []): PhpFile
    {
        $file = $this->builder->build();
        $interfaceName = sprintf('%sFactoryInterface', $entity);

        $namespace = sprintf('Ergonode\%s\Domain\Factory', ucfirst($module));
        $entityClass = sprintf('Ergonode\%s\Domain\Entity\%s', ucfirst($module), $entity);
        $entityIdClass = sprintf('Ergonode\%s\Domain\Entity\%sId', ucfirst($module), $entity);

        $properties = array_merge(['id' => $entityIdClass], $properties);
        $phpNamespace = $file->addNamespace($namespace);

        $phpClass = $phpNamespace->addInterface($interfaceName);
        $phpClass->addComment('Autogenerated interface');

        $phpClass->addMember($this->methodBuilder->build('create', $properties, $entityClass));

        return $file;
    }
}
