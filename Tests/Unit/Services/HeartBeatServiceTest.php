<?php

namespace XQueue\Typo3MaileonIntegration\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use XQueue\Typo3MaileonIntegration\Services\HeartBeatService;

class HeartBeatServiceTest extends TestCase
{
    public function testConstructorThrowsOnEmptyApiKey(): void
    {
        $this->expectException(\Exception::class);
        new HeartBeatService('');
    }

    public function testCreateClientHashUsesFirstAndLastCharacter(): void
    {
        $service = new HeartBeatService('some-api-key');

        $reflectionMethod = new \ReflectionMethod(HeartBeatService::class, 'createClientHash');
        $reflectionMethod->setAccessible(true);

        self::assertSame('Ae', $reflectionMethod->invoke($service, 'Acme'));
    }

    public function testCreateClientHashThrowsOnEmptyAccountName(): void
    {
        $service = new HeartBeatService('some-api-key');

        $reflectionMethod = new \ReflectionMethod(HeartBeatService::class, 'createClientHash');
        $reflectionMethod->setAccessible(true);

        $this->expectException(\Exception::class);
        $reflectionMethod->invoke($service, '   ');
    }
}
