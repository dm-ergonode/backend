<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\ExporterFile\Tests\Infrastructure\Handler;

use Ergonode\ExporterFile\Infrastructure\Handler\ProcessExportCommandHandler;
use PHPUnit\Framework\TestCase;
use Ergonode\Channel\Domain\Repository\ChannelRepositoryInterface;
use Ergonode\Exporter\Domain\Command\Export\ProcessExportCommand;
use Ergonode\Exporter\Domain\Repository\ExportRepositoryInterface;
use Ergonode\EventSourcing\Infrastructure\Bus\CommandBusInterface;
use Ergonode\Exporter\Domain\Entity\Export;

class ProcessExportCommandHandlerTest extends TestCase
{
    public function testHandling(): void
    {
        $command = $this->createMock(ProcessExportCommand::class);
        $channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $exportRepository = $this->createMock(ExportRepositoryInterface::class);
        $exportRepository->expects(self::once())->method('load')
            ->willReturn($this->createMock(Export::class));

        $commandBus = $this->createMock(CommandBusInterface::class);

        $handler = new ProcessExportCommandHandler($channelRepository, $exportRepository, $commandBus, []);
        $handler->__invoke($command);
    }
}
