<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Multimedia\Tests\Application\Request\ParamConverter;

use Ergonode\Multimedia\Application\Request\ParamConverter\MultimediaParamConverter;
use Ergonode\Multimedia\Domain\Entity\Multimedia;
use Ergonode\Multimedia\Domain\Repository\MultimediaRepositoryInterface;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class MultimediaParamConverterTest extends TestCase
{
    /**
     * @var Request|MockObject
     */
    private $request;

    /**
     * @var ParamConverter|MockObject
     */
    private $configuration;

    /**
     * @var MultimediaRepositoryInterface|MockObject
     */
    private $repository;

    /**
     * @var FilesystemInterface|MockObject
     */
    private $storage;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->configuration = $this->createMock(ParamConverter::class);
        $this->repository = $this->createMock(MultimediaRepositoryInterface::class);
        $this->storage = $this->createMock(FilesystemInterface::class);
    }

    public function testSupportedClass(): void
    {
        $this->request->method('get')->willReturn(null);
        $this->configuration->method('getClass')->willReturn(Multimedia::class);

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $this->assertTrue($paramConverter->supports($this->configuration));
    }

    public function testUnSupportedClass(): void
    {
        $this->request->method('get')->willReturn(null);
        $this->configuration->method('getClass')->willReturn('Any other class namespace');

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $this->assertFalse($paramConverter->supports($this->configuration));
    }

    public function testEmptyParameter(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\BadRequestHttpException::class);
        $this->request->method('get')->willReturn(null);

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $paramConverter->apply($this->request, $this->configuration);
    }

    public function testInvalidParameter(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\BadRequestHttpException::class);
        $this->request->method('get')->willReturn('incorrect uuid');

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $paramConverter->apply($this->request, $this->configuration);
    }

    public function testEntityNotExists(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->request->method('get')->willReturn(Uuid::uuid4()->toString());

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $paramConverter->apply($this->request, $this->configuration);
    }

    public function testFileNotExists(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\ConflictHttpException::class);
        $this->request->method('get')->willReturn(Uuid::uuid4()->toString());
        $this->repository->method('load')->willReturn($this->createMock(Multimedia::class));
        $this->storage->method('has')->willReturn(false);

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $paramConverter->apply($this->request, $this->configuration);
    }

    public function testEntityExists(): void
    {
        $this->request->method('get')->willReturn(Uuid::uuid4()->toString());
        $this->repository->method('load')->willReturn($this->createMock(Multimedia::class));
        $this->request->attributes = $this->createMock(ParameterBag::class);
        $this->request->attributes->expects($this->once())->method('set');
        $this->storage->method('has')->willReturn(true);

        $paramConverter = new MultimediaParamConverter($this->repository, $this->storage);
        $paramConverter->apply($this->request, $this->configuration);
    }
}
