<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueLanguageCode;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueLanguageCodeValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueLanguageCodeValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $languageService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueLanguageCodeValidator
     */
    private $validator;

    protected function setUp(): void
    {
        $this->languageService = $this->createMock(LanguageService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UniqueLanguageCodeValidator($this->languageService);
        $this->validator->initialize($this->executionContext);
    }

    public function testUnsupportedValueType()
    {
        $value = new stdClass();
        $this->languageService
            ->expects($this->never())
            ->method('loadLanguage');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueLanguageCode());
    }

    public function testInvalidLanguageCode()
    {
        $languageCode = '';
        $value = new LanguageCreateData([
            'languageCode' => $languageCode,
        ]);

        $this->languageService
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willThrowException(new InvalidArgumentException('languageCode', 'language code has an invalid value'));
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueLanguageCode());
    }

    public function testValid()
    {
        $languageCode = 'eng-GB';
        $value = new LanguageCreateData([
            'languageCode' => $languageCode,
        ]);

        $this->languageService
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willThrowException(new NotFoundException('Language', $languageCode));
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueLanguageCode());
    }

    public function testEditingLanguageIsValid()
    {
        $languageCode = 'eng-GB';
        $languageId = 123;

        $language = $this->getMockBuilder(Language::class)
            ->setConstructorArgs([['id' => $languageId]])
            ->getMockForAbstractClass();
        $value = new LanguageCreateData([
            'languageCode' => $languageCode,
            'language' => $language,
        ]);
        $returnedLanguage = $this->getMockBuilder(Language::class)
            ->setConstructorArgs([['id' => $languageId]])
            ->getMockForAbstractClass();
        $this->languageService
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willReturn($returnedLanguage);
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueLanguageCode());
    }

    public function testInvalid()
    {
        $languageCode = 'eng-GB';

        $language = $this->getMockBuilder(Language::class)
            ->setConstructorArgs([['id' => 123]])
            ->getMockForAbstractClass();
        $value = new LanguageCreateData([
            'languageCode' => $languageCode,
            'language' => $language,
        ]);
        $returnedLanguage = $this->getMockBuilder(Language::class)
            ->setConstructorArgs([['id' => 456]])
            ->getMockForAbstractClass();
        $this->languageService
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willReturn($returnedLanguage);
        $constraint = new UniqueLanguageCode();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('language_code')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('%language_code%', $languageCode)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($value, $constraint);
    }
}
