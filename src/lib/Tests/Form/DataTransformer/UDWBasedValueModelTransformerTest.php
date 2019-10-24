<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueModelTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UDWBasedValueModelTransformerTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueModelTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->transformer = new UDWBasedValueModelTransformer(
            $this->locationService
        );
    }

    /**
     * @dataProvider dataProviderForTransform
     */
    public function testTransform(?array $given, ?array $expected)
    {
        $this->locationService
            ->method('loadLocation')
            ->willReturnCallback(function ($id) {
                return $this->createLocation($id);
            });

        $this->assertEquals($expected, $this->transformer->transform($given));
    }

    public function dataProviderForTransform(): array
    {
        return [
            [null, null],
            [
                [
                    '/1/2/54/',
                    '/1/2/54/56/',
                    '/1/2/54/58/',
                ],
                [
                    $this->createLocation(54),
                    $this->createLocation(56),
                    $this->createLocation(58),
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTransformThrowsTransformationFailedException
     */
    public function testTransformThrowsTransformationFailedException(string $exceptionClass)
    {
        $this->expectException(TransformationFailedException::class);

        $this->locationService
            ->expects($this->any())
            ->method('loadLocation')
            ->willThrowException(
                $this->createMock($exceptionClass)
            );

        $this->transformer->transform(['/1/2/54']);
    }

    public function dataProviderForTransformThrowsTransformationFailedException(): array
    {
        return [
            [NotFoundException::class],
            [UnauthorizedException::class],
        ];
    }

    /**
     * @dataProvider dataProviderForReverseTransform
     */
    public function testReverseTransform(?array $given, ?array $expected)
    {
        $this->assertEquals($expected, $this->transformer->reverseTransform($given));
    }

    public function dataProviderForReverseTransform(): array
    {
        return [
            [null, null],
            [
                [
                    $this->createLocation(54),
                    $this->createLocation(56),
                    $this->createLocation(58),
                ],
                [54, 56, 58],
            ],
        ];
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
