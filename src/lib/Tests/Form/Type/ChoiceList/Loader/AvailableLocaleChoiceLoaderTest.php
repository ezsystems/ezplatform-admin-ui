<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\Type\ChoiceList\Loader;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\AvailableLocaleChoiceLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableLocaleChoiceLoaderTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $validator;

    /** @var \Symfony\Component\Validator\Constraints\Locale|\PHPUnit\Framework\MockObject\MockObject */
    private $localeConstraint;

    /** @var \Symfony\Component\Validator\ConstraintViolationInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $constraintViolation;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    protected function setUp()
    {
        parent::setUp();

        $this->localeConstraint = $this->createMock(Locale::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    /**
     * @dataProvider providerForGetChoiceList
     *
     * @param array $availableTranslations
     * @param array $additionalTranslations
     * @param array $expectedLocales
     */
    public function testGetChoiceList(
        array $availableTranslations,
        array $additionalTranslations,
        array $expectedLocales
    ): void {
        $this->validator
            ->method('validate')
            ->willReturnCallback(function ($locale) {
                return $locale === 'foo_BAR' ? new ConstraintViolationList([$this->constraintViolation]) : new ConstraintViolationList();
            });

        $this->configResolver
            ->method('getParameter')
            ->with('user_preferences.additional_translations')
            ->willReturn($additionalTranslations);

        $availableLocaleChoiceLoader = new AvailableLocaleChoiceLoader(
            $this->validator,
            $this->configResolver,
            $availableTranslations
        );

        $this->assertSame($expectedLocales, $availableLocaleChoiceLoader->getChoiceList());
    }

    public function providerForGetChoiceList(): array
    {
        return [
            'available_translations' => [
                ['en', 'nb_NO'],
                [],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                ],
            ],
            'available_and_additional_translations' => [
                ['en', 'nb_NO'],
                ['de_DE'],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                    'German (Germany)' => 'de_DE',
                ],
            ],
            'unsupported_translation' => [
                ['en', 'nb_NO'],
                ['de_DE', 'foo_BAR'],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                    'German (Germany)' => 'de_DE',
                ],
            ],
        ];
    }
}
