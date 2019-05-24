<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\UI\Config\Mapper\FieldType\RichText;

use EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\OnlineEditor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class OnlineEditorTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\OnlineEditorConfigMapper */
    private $mapper;

    public function setUp()
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->willReturnArgument(0);
        /** @var \Symfony\Component\Translation\TranslatorInterface $translatorMock */
        $this->mapper = new OnlineEditor($translatorMock, 'online_editor');
    }

    /**
     * Data provider for mapCssClassesConfiguration.
     *
     * @see testMapCssClassesConfiguration
     *
     * @return array
     */
    public function getSemanticConfigurationForMapCssClassesConfiguration(): array
    {
        return [
            [
                // semantic configuration ...
                [
                    'paragraph' => [
                        'choices' => ['class1', 'class2'],
                        'required' => true,
                        'default_value' => 'class1',
                        'multiple' => true,
                    ],
                    'table' => [
                        'choices' => ['class1', 'class2'],
                        'required' => false,
                        'default_value' => 'class2',
                        'multiple' => false,
                    ],
                    'heading' => [
                        'choices' => ['class1', 'class2'],
                        'required' => false,
                        'multiple' => false,
                    ],
                ],
                // ... is mapped to:
                [
                    'paragraph' => [
                        'choices' => ['class1', 'class2'],
                        'required' => true,
                        'defaultValue' => 'class1',
                        'multiple' => true,
                        'label' => 'ezrichtext.classes.class.label',
                    ],
                    'table' => [
                        'choices' => ['class1', 'class2'],
                        'required' => false,
                        'defaultValue' => 'class2',
                        'multiple' => false,
                        'label' => 'ezrichtext.classes.class.label',
                    ],
                    'heading' => [
                        'choices' => ['class1', 'class2'],
                        'required' => false,
                        'defaultValue' => null,
                        'multiple' => false,
                        'label' => 'ezrichtext.classes.class.label',
                    ],
                ],
            ],
        ];
    }

    /**
     * Data provider for mapDataAttributesConfiguration.
     *
     * @see testMapDataAttributesConfigura
     *
     * @return array
     */
    public function getSemanticConfigurationForMapDataAttributesConfiguration(): array
    {
        return [
            [
                // semantic configuration ...
                [
                    'paragraph' => [
                        'select-multiple-attr' => [
                            'type' => 'choice',
                            'multiple' => true,
                            'required' => true,
                            'choices' => ['value1', 'value2'],
                            'default_value' => 'value2',
                        ],
                        'select-single-attr' => [
                            'type' => 'choice',
                            'multiple' => false,
                            'required' => true,
                            'choices' => ['value1', 'value2'],
                            'default_value' => 'value2',
                        ],
                    ],
                    'heading' => [
                        'boolean-attr' => [
                            'type' => 'boolean',
                            'required' => false,
                            'default_value' => true,
                        ],
                        'text-attr' => [
                            'type' => 'string',
                            'default_value' => 'foo',
                            'required' => true,
                        ],
                    ],
                    'tr' => [
                        'number-attr' => [
                            'type' => 'number',
                            'default_value' => 1,
                            'required' => true,
                        ],
                    ],
                ],
                // ... is mapped to:
                [
                    'paragraph' => [
                        'select-multiple-attr' => [
                            'label' => 'ezrichtext.attributes.paragraph.select-multiple-attr.label',
                            'type' => 'choice',
                            'multiple' => true,
                            'required' => true,
                            'choices' => ['value1', 'value2'],
                            'defaultValue' => 'value2',
                        ],
                        'select-single-attr' => [
                            'label' => 'ezrichtext.attributes.paragraph.select-single-attr.label',
                            'type' => 'choice',
                            'multiple' => false,
                            'required' => true,
                            'choices' => ['value1', 'value2'],
                            'defaultValue' => 'value2',
                        ],
                    ],
                    'heading' => [
                        'boolean-attr' => [
                            'label' => 'ezrichtext.attributes.heading.boolean-attr.label',
                            'type' => 'boolean',
                            'required' => false,
                            'defaultValue' => true,
                        ],
                        'text-attr' => [
                            'label' => 'ezrichtext.attributes.heading.text-attr.label',
                            'type' => 'string',
                            'defaultValue' => 'foo',
                            'required' => true,
                        ],
                    ],
                    'tr' => [
                        'number-attr' => [
                            'label' => 'ezrichtext.attributes.tr.number-attr.label',
                            'type' => 'number',
                            'defaultValue' => 1,
                            'required' => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getSemanticConfigurationForMapCssClassesConfiguration
     *
     * @param array $semanticConfiguration
     * @param array $expectedMappedConfiguration
     */
    public function testMapCssClassesConfiguration(
        array $semanticConfiguration,
        array $expectedMappedConfiguration
    ): void {
        self::assertEquals(
            $expectedMappedConfiguration,
            $this->mapper->mapCssClassesConfiguration($semanticConfiguration)
        );
    }

    /**
     * @dataProvider getSemanticConfigurationForMapDataAttributesConfiguration
     *
     * @param array $semanticConfiguration
     * @param array $expectedMappedConfiguration
     */
    public function testMapDataAttributesConfigura(
        array $semanticConfiguration,
        array $expectedMappedConfiguration
    ): void {
        self::assertEquals(
            $expectedMappedConfiguration,
            $this->mapper->mapDataAttributesConfiguration($semanticConfiguration)
        );
    }
}
