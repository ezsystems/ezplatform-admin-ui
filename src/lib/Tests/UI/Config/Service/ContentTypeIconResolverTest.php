<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\UI\Config\Service;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

class ContentTypeIconResolverTest extends TestCase
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Asset\Packages */
    private $packages;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver */
    private $contentTypeIconResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->packages = $this->createMock(Packages::class);

        $this->contentTypeIconResolver = new ContentTypeIconResolver(
            $this->configResolver,
            $this->packages
        );
    }

    /**
     * @dataProvider dataProviderForGetContentTypeIcon
     */
    public function testGetContentTypeIcon(array $config, string $identifier, string $expected)
    {
        $this->configResolver
            ->expects($this->any())
            ->method('hasParameter')
            ->willReturnCallback(static function (string $key) use ($config) {
                $key = explode('.', $key);

                return isset($config[array_pop($key)]);
            });

        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->willReturnCallback(static function (string $key) use ($config) {
                $key = explode('.', $key);

                return $config[array_pop($key)];
            });

        $this->packages
            ->expects($this->any())
            ->method('getUrl')
            ->willReturnCallback(static function (string $uri) {
                return "https://cdn.example.com/$uri";
            });

        $this->assertEquals($expected, $this->contentTypeIconResolver->getContentTypeIcon($identifier));
    }

    public function dataProviderForGetContentTypeIcon(): array
    {
        return [
            [
                [
                    'custom' => [
                        'thumbnail' => 'icon.svg#custom',
                    ],
                    'default-config' => [
                        'thumbnail' => 'icon.svg#default',
                    ],
                ],
                'custom',
                'https://cdn.example.com/icon.svg#custom',
            ],
            [
                [
                    'custom-without-fragment' => [
                        'thumbnail' => 'icon.png',
                    ],
                    'default-config' => [
                        'thumbnail' => 'icon.svg#default',
                    ],
                ],
                'custom-without-fragment',
                'https://cdn.example.com/icon.png',
            ],
            [
                [
                    'custom-without-icon' => [
                        'thumbnail' => null,
                    ],
                    'default-config' => [
                        'thumbnail' => 'icon.svg#default',
                    ],
                ],
                'custom-without-icon',
                'https://cdn.example.com/icon.svg#default',
            ],
            [
                [
                    'default-config' => [
                        'thumbnail' => 'icon.svg#default',
                    ],
                ],
                'custom-with-missing-config',
                'https://cdn.example.com/icon.svg#default',
            ],
        ];
    }
}
