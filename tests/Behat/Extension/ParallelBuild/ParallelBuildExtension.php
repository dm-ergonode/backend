<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace App\Tests\Behat\Extension\ParallelBuild;

use App\Tests\Behat\Extension\ParallelBuild\Cli\ParallelScenarioController;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use Behat\Testwork\Tester\Exercise;
use Behat\Testwork\Tester\ServiceContainer\TesterExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParallelBuildExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'ergonode_parallel_build';
    }

    /**
     * @inheritDoc
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(
            ParallelScenarioController::class,
            [
                new Reference(CliExtension::CONTROLLER_TAG . '.parallel_exercise.inner'),
                new Reference(SuiteExtension::REGISTRY_ID),
                new Reference(SpecificationExtension::FINDER_ID),
                new Reference(TesterExtension::EXERCISE_ID)
                //new Reference(ParallelWorkerFactory::class),
            ]
        );
        $definition
            ->setDecoratedService(CliExtension::CONTROLLER_TAG . '.exercise');
        $container->setDefinition(CliExtension::CONTROLLER_TAG . '.parallel_exercise', $definition);
    }


    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
