<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Attribute\Application\Model\Attribute;

use Ergonode\Attribute\Infrastructure\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class AttributeFormModel
{
    /**
     * @Assert\NotBlank(
     *     message="System name is required",
     *     groups={"Create"}
     *     )
     * @Assert\Length(
     *     max=128,
     *     maxMessage="Maximum number of characters is 128",
     *     groups={"Create"}
     *     )
     * @AppAssert\AttributeCode(
     *     groups={"Create"}
     *     )
     */
    public ?string $code = null;

    /**
     * @Assert\NotBlank(
     *     message="Attribute scope is required",
     *     )
     * @Assert\Choice({"local", "global"})
     */
    public ?string $scope = null;

    /**
     * @var array
     *
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Length(
     *      max=128,
     *      maxMessage="Attribute name is too long. It should contain {{ limit }} characters or less."
     *     )
     * })
     */
    public array $label = [];

    /**
     * @var array
     *
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Length(
     *       max=4000,
     *       maxMessage="Attribute placeholder is too long. It should contain {{ limit }} characters or less."
     *     )
     * })
     */
    public array $placeholder = [];

    /**
     * @var array
     *
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Length(
     *       max=4000,
     *       maxMessage="Attribute hint is too long. It should contain {{ limit }} characters or less."
     *     )
     * })
     */
    public array $hint = [];

    /**
     * @var array
     */
    public array $groups = [];
}
