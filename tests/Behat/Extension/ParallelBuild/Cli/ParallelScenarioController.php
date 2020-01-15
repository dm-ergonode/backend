<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);


namespace App\Tests\Behat\Extension\ParallelBuild\Cli;


use App\Tests\Behat\Extension\ParallelBuild\ServiceContainer\SpecificationsFinder;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Specification\SpecificationFinder;
use Behat\Testwork\Tester\Cli\ExerciseController;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParallelScenarioController implements Controller
{
    /**
     * @var ExerciseController
     */
    private $decoratedExerciseController;

    /**
     * @var SpecificationFinder
     */
    private $specificationFinder;


    /**
     * @param ExerciseController $decoratedExerciseController
     * @param SpecificationsFinder $specificationsFinder
     */
    public function __construct(
        ExerciseController $decoratedExerciseController,
        SpecificationsFinder $specificationsFinder
    ) {
        $this->decoratedExerciseController = $decoratedExerciseController;
        $this->specificationFinder = $specificationsFinder;
    }

    /**
     * @inheritDoc
     */
    public function configure(SymfonyCommand $command)
    {
        $this->decoratedExerciseController->configure($command);
        $command->addOption(
            'parallel',
            'l',
            InputOption::VALUE_OPTIONAL,
            'How many jobs run in parallel? Available values empty or integer',
            false
        )
            ->addOption(
                'feature-mode',
                null,
                InputOption::VALUE_OPTIONAL,
                'Paralleled only features',
                false
            )
            ->addUsage('--parallel 8')
            ->addUsage('--parallel');
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $input->getOption('parallel') !== false;
        if (! $startInParallel) {
            return $this->decoratedExerciseController->execute($input, $output);
        }

        $specs = $this->findSpecifications($input);
    }

    /**
     * @param InputInterface $input
     * @return array|string[]
     */
    private function findSpecifications(InputInterface $input)
    {
        $isFeatureMode  = $input->getOption('feature-mode') !== false;
        if ($isFeatureMode) {
            return $this->specificationFinder->findFeatures($input->getArgument('path'));
        }

        return $this->specificationFinder->findScenarios($input->getArgument('path'));
    }
}
