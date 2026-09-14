<?php

namespace XQueue\Typo3MaileonIntegration\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Form\Domain\Model\FormDefinition;
use TYPO3\CMS\Form\Domain\Model\FormElements\FormElementInterface;
use XQueue\Typo3MaileonIntegration\Services\FormProcessingService;

/**
 * Exercises FormProcessingService's pure-logic methods directly via reflection,
 * bypassing the constructor (which performs live HTTP calls against the
 * Maileon API to validate the configured API key).
 */
class FormProcessingServiceTest extends TestCase
{
    protected FormProcessingService $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = (new \ReflectionClass(FormProcessingService::class))->newInstanceWithoutConstructor();
    }

    protected function invoke(string $method, array $arguments = []): mixed
    {
        $reflectionMethod = new \ReflectionMethod(FormProcessingService::class, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($this->subject, $arguments);
    }

    protected function mockElement(string $identifier, string $type, ?string $maileonFieldName): FormElementInterface
    {
        $element = self::createStub(FormElementInterface::class);
        $element->method('getIdentifier')->willReturn($identifier);
        $element->method('getType')->willReturn($type);
        $element->method('getProperties')->willReturn(
            $maileonFieldName !== null ? ['maileonFieldName' => $maileonFieldName] : []
        );

        return $element;
    }

    public function testExtractFormValuesMapsEmailFieldCaseInsensitively(): void
    {
        $formDefinition = self::createStub(FormDefinition::class);
        $formDefinition->method('getElements')->willReturn([
            $this->mockElement('email-1', 'Email', 'Email'),
        ]);

        [$email, $standardFields, $customFields] = $this->invoke('extractFormValues', [
            ['email-1' => 'jane@example.com'],
            $formDefinition,
        ]);

        self::assertSame('jane@example.com', $email);
        self::assertSame([], $standardFields);
        self::assertArrayHasKey('Typo3_created', $customFields);
    }

    public function testExtractFormValuesSeparatesStandardAndCustomFields(): void
    {
        $formDefinition = self::createStub(FormDefinition::class);
        $formDefinition->method('getElements')->willReturn([
            $this->mockElement('firstname-1', 'Text', 'firstname'),
            $this->mockElement('favcolor-1', 'Text', 'favouriteColor'),
        ]);

        [, $standardFields, $customFields] = $this->invoke('extractFormValues', [
            ['firstname-1' => 'Jane', 'favcolor-1' => 'blue'],
            $formDefinition,
        ]);

        self::assertSame(['FIRSTNAME' => 'Jane'], $standardFields);
        self::assertSame('blue|string', $customFields['favouriteColor']);
    }

    public function testExtractFormValuesSkipsElementsWithoutMaileonFieldName(): void
    {
        $formDefinition = self::createStub(FormDefinition::class);
        $formDefinition->method('getElements')->willReturn([
            $this->mockElement('unmapped-1', 'Text', null),
        ]);

        [$email, $standardFields, $customFields] = $this->invoke('extractFormValues', [
            ['unmapped-1' => 'ignored'],
            $formDefinition,
        ]);

        self::assertNull($email);
        self::assertSame([], $standardFields);
        self::assertSame(['Typo3_created' => true], $customFields);
    }

    public function testValidateStandardFieldsAcceptsKnownGenderValues(): void
    {
        $this->invoke('validateStandardFields', [['GENDER' => 'F']]);
        $this->invoke('validateStandardFields', [['GENDER' => 'm']]);
        $this->invoke('validateStandardFields', [['GENDER' => 'd']]);
        $this->addToAssertionCount(3);
    }

    public function testValidateStandardFieldsRejectsInvalidGender(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->invoke('validateStandardFields', [['GENDER' => 'x']]);
    }

    public function testValidateStandardFieldsRejectsInvalidLocale(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->invoke('validateStandardFields', [['LOCALE' => 'eng']]);
    }

    public function testValidateStandardFieldsAcceptsValidLocale(): void
    {
        $this->invoke('validateStandardFields', [['LOCALE' => 'en']]);
        $this->addToAssertionCount(1);
    }

    public function testConvertValueByTypeCastsCheckboxToBool(): void
    {
        self::assertTrue($this->invoke('convertValueByType', ['Checkbox', '1']));
        self::assertFalse($this->invoke('convertValueByType', ['Checkbox', '']));
    }

    public function testConvertValueByTypeFormatsDateTimeInterfaceToYmd(): void
    {
        $date = new \DateTimeImmutable('2026-03-05');
        self::assertSame('2026-03-05', $this->invoke('convertValueByType', ['Date', $date]));
    }

    public function testConvertValueByTypeParsesDateStringToYmd(): void
    {
        self::assertSame('2026-03-05', $this->invoke('convertValueByType', ['Date', '2026-03-05']));
    }

    public function testConvertValueByTypeReturnsOriginalValueOnInvalidDate(): void
    {
        self::assertSame('not-a-date', $this->invoke('convertValueByType', ['Date', 'not-a-date']));
    }

    public function testConvertValueByTypeCastsNumberToInt(): void
    {
        self::assertSame(42, $this->invoke('convertValueByType', ['Number', '42']));
    }

    public function testConvertValueByTypeTruncatesDefaultStringTo255Characters(): void
    {
        $result = $this->invoke('convertValueByType', ['Text', str_repeat('a', 300)]);

        self::assertSame(255, mb_strlen($result));
    }

    public function testResolveMaileonCustomFieldTypeMapsKnownFormElementType(): void
    {
        self::assertSame('boolean', $this->invoke('resolveMaileonCustomFieldType', ['Checkbox']));
    }

    public function testResolveMaileonCustomFieldTypeFallsBackToString(): void
    {
        self::assertSame('string', $this->invoke('resolveMaileonCustomFieldType', ['SomeUnknownType']));
    }
}
