<?php
/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Attribute\Tests\Application\Form\Transformer;

use Ergonode\Attribute\Application\Form\Transformer\AttributeGroupCodeDataTransformer;
use Ergonode\Attribute\Domain\ValueObject\AttributeGroupCode;
use PHPUnit\Framework\TestCase;

class AttributeGroupCodeDataTransformerTest extends TestCase
{
    protected AttributeGroupCodeDataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new AttributeGroupCodeDataTransformer();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testTransform(?AttributeGroupCode $code, ?string $string): void
    {
        self::assertSame($string, $this->transformer->transform($code));
    }

    public function testTransformException(): void
    {
        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);
        $value = new \stdClass();
        $this->transformer->transform($value);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReverseTransform(?AttributeGroupCode $code, ?string $string): void
    {
        self::assertEquals($code, $this->transformer->reverseTransform($string));
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                'attributeTypeValueObject' => new AttributeGroupCode('group_code'),
                'string' => 'group_code',
            ],
            [
                'attributeTypeValueObject' => null,
                'string' => null,
            ],
        ];
    }
}
