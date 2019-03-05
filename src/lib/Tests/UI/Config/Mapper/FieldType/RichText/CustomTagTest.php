<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText;

use ArrayObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * UI Config Mapper test for RichText Custom Tags configuration.
 *
 * @see \EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag
 */
class CustomTagTest extends TestCase
{
    /**
     * @covers \EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag::mapConfig
     *
     * @dataProvider providerForTestMapConfig
     */
    public function testMapConfig(array $customTagsConfiguration, array $enabledCustomTags, array $expectedConfig)
    {
        $mapper = new CustomTag(
            $customTagsConfiguration,
            $this->getTranslatorMock(),
            'custom_tags',
            $this->getPackagesMock(),
            new ArrayObject([
                new CustomTag\ChoiceAttributeMapper(),
                new CustomTag\CommonAttributeMapper(),
            ])
        );

        $actualConfig = $mapper->mapConfig($enabledCustomTags);

        self::assertEquals($expectedConfig, $actualConfig);
    }

    /**
     * Data provider for {@see testMapConfig}.
     *
     * @return array
     */
    public function providerForTestMapConfig(): array
    {
        return [
            [
                [
                    'ezyoutube' => [
                        'template' => '@ezdesign/fields/ezrichtext/custom_tags/ezyoutube.html.twig',
                        'icon' => '/bundles/ezplatformadminui/img/ez-icons.svg#video',
                        'is_inline' => false,
                        'attributes' => [
                            'width' => [
                                'type' => 'number',
                                'required' => true,
                                'default_value' => 640,
                            ],
                            'height' => [
                                'type' => 'number',
                                'required' => true,
                                'default_value' => 360,
                            ],
                            'autoplay' => [
                                'type' => 'boolean',
                                'default_value' => false,
                                'required' => false,
                            ],
                        ],
                    ],
                    'eztwitter' => [
                        'template' => '@ezdesign/fields/ezrichtext/custom_tags/eztwitter.html.twig',
                        'icon' => '/bundles/ezplatformadminui/img/ez-icons.svg#twitter',
                        'is_inline' => false,
                        'attributes' => [
                            'tweet_url' => [
                                'type' => 'string',
                                'required' => true,
                                'default_value' => null,
                            ],
                            'cards' => [
                                'type' => 'choice',
                                'required' => false,
                                'default_value' => '',
                                'choices' => [
                                    '',
                                    'hidden',
                                ],
                            ],
                        ],
                    ],
                ],
                ['ezyoutube', 'eztwitter'],
                [
                    'ezyoutube' => [
                        'label' => 'ezrichtext.custom_tags.ezyoutube.label',
                        'description' => 'ezrichtext.custom_tags.ezyoutube.description',
                        'icon' => '/bundles/ezplatformadminui/img/ez-icons.svg#video',
                        'isInline' => false,
                        'attributes' => [
                            'width' => [
                                'label' => 'ezrichtext.custom_tags.ezyoutube.attributes.width.label',
                                'type' => 'number',
                                'required' => true,
                                'defaultValue' => 640,
                            ],
                            'height' => [
                                'label' => 'ezrichtext.custom_tags.ezyoutube.attributes.height.label',
                                'type' => 'number',
                                'required' => true,
                                'defaultValue' => 360,
                            ],
                            'autoplay' => [
                                'label' => 'ezrichtext.custom_tags.ezyoutube.attributes.autoplay.label',
                                'type' => 'boolean',
                                'required' => false,
                                'defaultValue' => false,
                            ],
                        ],
                    ],
                    'eztwitter' => [
                        'label' => 'ezrichtext.custom_tags.eztwitter.label',
                        'description' => 'ezrichtext.custom_tags.eztwitter.description',
                        'icon' => '/bundles/ezplatformadminui/img/ez-icons.svg#twitter',
                        'isInline' => false,
                        'attributes' => [
                            'tweet_url' => [
                                'label' => 'ezrichtext.custom_tags.eztwitter.attributes.tweet_url.label',
                                'type' => 'string',
                                'required' => true,
                                'defaultValue' => null,
                            ],
                            'cards' => [
                                'label' => 'ezrichtext.custom_tags.eztwitter.attributes.cards.label',
                                'type' => 'choice',
                                'required' => false,
                                'defaultValue' => '',
                                'choices' => [
                                    '',
                                    'hidden',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTranslatorMock(): MockObject
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->withAnyParameters()
            ->willReturnArgument(0);

        return $translatorMock;
    }

    /**
     * @return \Symfony\Component\Asset\Packages|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPackagesMock(): MockObject
    {
        $packagesMock = $this->createMock(Packages::class);
        $packagesMock
            ->expects($this->any())
            ->method('getUrl')
            ->withAnyParameters()
            ->willReturnArgument(0);

        return $packagesMock;
    }
}
