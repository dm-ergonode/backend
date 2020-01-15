<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);


namespace App\Tests\Behat\Extension\ParallelBuild\ServiceContainer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Specification\GroupedSpecificationIterator;
use Behat\Testwork\Specification\SpecificationFinder;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Suite\SuiteRepository;

class SpecificationsFinder
{
    /**
     * @var SuiteRepository
     */
    private $suiteRepository;

    /**
     * @var SpecificationFinder
     */
    private $specificationFinder;

    /**
     * @param SuiteRepository $suiteRepository
     * @param SpecificationFinder $specificationFinder
     */
    public function __construct(
        SuiteRepository $suiteRepository,
        SpecificationFinder $specificationFinder
    ) {
        $this->suiteRepository = $suiteRepository;
        $this->specificationFinder = $specificationFinder;
    }

    /**
     * @param string $path
     * @return array|string[]
     */
    public function findFeatures(string $path)
    {
        $suites = $this->findSuites($path);
        $features = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                /**
                 * @var $feature FeatureNode
                 */
                $features[] = $feature->getFile();
            }
        }

        return $features;
    }

    /**
     * @param string $path
     * @return array|string[]
     */
    public function findScenarios(string $path)
    {
        $suites = $this->findSuites($path);
        $scenarios = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                /**
                 * @var $feature FeatureNode
                 */
                foreach ($feature->getScenarios() as $scenario) {
                    $scenarios[] = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
                }
            }
        }

        return $scenarios;
    }

    /**
     * @param string $path
     * @return GroupedSpecificationIterator[]
     */
    private function findSuites(string $path)
    {
        $specs = $this->findSpecifications($path);
        return GroupedSpecificationIterator::group($specs);
    }

    /**
     * Finds exercise specifications.
     *
     * @param string $path
     *
     * @return SpecificationIterator[]
     */
    private function findSpecifications(string $path)
    {
        return $this->findSuitesSpecifications($this->getAvailableSuites(), $path);
    }

    /**
     * Finds specification iterators for all provided suites using locator.
     *
     * @param Suite[]     $suites
     * @param null|string $locator
     *
     * @return SpecificationIterator[]
     */
    private function findSuitesSpecifications($suites, $locator)
    {
        return $this->specificationFinder->findSuitesSpecifications($suites, $locator);
    }

    /**
     * Returns all currently available suites.
     *
     * @return Suite[]
     */
    private function getAvailableSuites()
    {
        return $this->suiteRepository->getSuites();
    }
}