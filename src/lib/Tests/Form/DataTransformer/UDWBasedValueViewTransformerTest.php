<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueViewTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UDWBasedValueViewTransformerTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueViewTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->transformer = new UDWBasedValueViewTransformer(
            $this->locationService
        );
    }

    /**
     * @dataProvider dataProviderForTransform
     */
    public function testTransform(?array $given, ?string $expected)
    {
        $this->assertEquals($expected, $this->transformer->transform($given));
    }

    public function dataProviderForTransform(): array
    {
        return [
            [null, null],
            [
                [
                    $this->createLocation(54),
                    $this->createLocation(56),
                    $this->createLocation(58),
                ],
                '54,56,58',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForReverseTransform
     */
    public function testReverseTransform(?string $given, ?array $expected)
    {
        $this->locationService
            ->method('loadLocation')
            ->willReturnCallback(function ($id) {
                return $this->createLocation($id);
            });

        $this->assertEquals($expected, $this->transformer->reverseTransform($given));
    }

    public function dataProviderForReverseTransform(): array
    {
        return [
            [null, null],
            [
                '54,56,58',
                [
                    $this->createLocation(54),
                    $this->createLocation(56),
                    $this->createLocation(58),
                ],
            ],
        ];
    }

    public function testTransformWithDeletedLocation(): void
    {
        $this->locationService
            ->method('loadLocation')
            ->willThrowException(
                $this->createMock(NotFoundException::class)
            );

        self::assertEmpty($this->transformer->transform(['/1/2/54']));
    }

    public function testReverseTransformThrowsTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->locationService
            ->method('loadLocation')
            ->willThrowException(
                $this->createMock(UnauthorizedException::class)
            );

        $this->transformer->reverseTransform('54,56,58');
    }

    private function createLocation($id): Location
    {
        $location = $this->createMock(Location::class);
        $location
            ->method('__get')
            ->with('id')
            ->willReturn($id);
        $location
            ->method('__isset')
            ->with('id')
            ->willReturn(true);

        return $location;
    }
}
