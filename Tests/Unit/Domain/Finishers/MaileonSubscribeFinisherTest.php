<?php

namespace XQueue\Typo3MaileonIntegration\Tests\Unit\Domain\Finishers;

use PHPUnit\Framework\TestCase;
use XQueue\Typo3MaileonIntegration\Domain\Finishers\MaileonSubscribeFinisher;

class MaileonSubscribeFinisherTest extends TestCase
{
    protected function parseFinisherSettings(array $options): array
    {
        $finisher = new MaileonSubscribeFinisher();
        $finisher->setOptions($options);

        $reflectionMethod = new \ReflectionMethod(MaileonSubscribeFinisher::class, 'parseFinisherSettings');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke($finisher);
    }

    public function testDefaultsAreUsedWhenNoOptionsAreGiven(): void
    {
        $settings = $this->parseFinisherSettings([]);

        self::assertSame([
            'permission' => 'none',
            'enableDoiProcess' => false,
            'finalPermission' => 'doi+',
            'doiKey' => '',
        ], $settings);
    }

    public function testProvidedOptionsOverrideDefaults(): void
    {
        $settings = $this->parseFinisherSettings([
            'permission' => 'confirmed',
            'enableDoiProcess' => 'true',
            'finalPermission' => 'doi',
            'doiKey' => 'my-doi-key',
        ]);

        self::assertSame([
            'permission' => 'confirmed',
            'enableDoiProcess' => true,
            'finalPermission' => 'doi',
            'doiKey' => 'my-doi-key',
        ], $settings);
    }

    public function testEnableDoiProcessAcceptsBooleanLikeStrings(): void
    {
        self::assertFalse($this->parseFinisherSettings(['enableDoiProcess' => '0'])['enableDoiProcess']);
        self::assertFalse($this->parseFinisherSettings(['enableDoiProcess' => 'false'])['enableDoiProcess']);
        self::assertTrue($this->parseFinisherSettings(['enableDoiProcess' => '1'])['enableDoiProcess']);
    }
}
