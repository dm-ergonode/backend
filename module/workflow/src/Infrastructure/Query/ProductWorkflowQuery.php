<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Workflow\Infrastructure\Query;

use Ergonode\Workflow\Domain\Entity\Attribute\StatusSystemAttribute;
use Ergonode\Value\Domain\ValueObject\StringValue;
use Ergonode\Attribute\Domain\ValueObject\AttributeCode;
use Ergonode\SharedKernel\Domain\Aggregate\StatusId;
use Webmozart\Assert\Assert;
use Ergonode\SharedKernel\Domain\Aggregate\AttributeId;
use Ergonode\Workflow\Domain\Provider\WorkflowProvider;
use Ergonode\Workflow\Domain\Repository\StatusRepositoryInterface;
use Ergonode\Workflow\Domain\Service\StatusCalculationService;
use Ergonode\Product\Domain\Entity\AbstractProduct;
use Ergonode\Core\Domain\ValueObject\Language;

class ProductWorkflowQuery
{
    private WorkflowProvider $workflowProvider;

    private StatusRepositoryInterface $statusRepository;

    private StatusCalculationService $service;

    public function __construct(
        WorkflowProvider $workflowProvider,
        StatusRepositoryInterface $statusRepository,
        StatusCalculationService $service
    ) {
        $this->workflowProvider = $workflowProvider;
        $this->statusRepository = $statusRepository;
        $this->service = $service;
    }

    /**
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getQuery(AbstractProduct $product, Language $language, Language $productLanguage): array
    {
        $code = new AttributeCode(StatusSystemAttribute::CODE);
        $result = [];
        if ($product->hasAttribute($code)) {
            $workflow = $this->workflowProvider->provide();
            /** @var StringValue $value */
            $value = $product->getAttribute($code)->getValue();
            $statusId = new StatusId($value[$productLanguage->getCode()]);
            $status = $this->statusRepository->load($statusId);
            Assert::notNull($status, sprintf('status %s not exists', $statusId->getValue()));
            $result['status'] = [
                'attribute_id' => AttributeId::fromKey((new AttributeCode(StatusSystemAttribute::CODE))->getValue()),
                'id' => $status->getId()->getValue(),
                'name' => $status->getName()->get($language),
                'code' => $status->getCode()->getValue(),
                'color' => $status->getColor(),
            ];

            $transitions = $workflow->getTransitionsFromStatus($statusId);
            $result['workflow'] = [];
            foreach ($transitions as $transition) {
                if ($this->service->available($transition, $product)) {
                    $destinationStatus = $this->statusRepository->load($transition->getTo());
                    Assert::notNull($destinationStatus);
                    $result['workflow'][] = [
                        'id' => $destinationStatus->getId()->getValue(),
                        'name' => $destinationStatus->getName()->get($language),
                        'code' => $destinationStatus->getCode(),
                        'color' => $destinationStatus->getColor(),
                    ];
                }
            }
        }

        return $result;
    }
}
