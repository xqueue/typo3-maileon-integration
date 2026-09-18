<?php

namespace XQueue\Typo3MaileonIntegration\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use XQueue\Typo3MaileonIntegration\ViewHelpers\LinkLabelViewHelper;

class LinkLabelViewHelperTest extends TestCase
{
    protected function render(string $url, string $label): string
    {
        $viewHelper = new LinkLabelViewHelper();
        $viewHelper->setArguments(['url' => $url, 'label' => $label]);

        return $viewHelper->render();
    }

    public function testBracketedPartBecomesLinkTextWithSurroundingTextPreserved(): void
    {
        $result = $this->render('/privacy', 'I accept the [Privacy Policy].');

        self::assertSame(
            'I accept the <a href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.',
            $result
        );
    }

    public function testLabelWithoutBracketsLinksTheWholeLabel(): void
    {
        $result = $this->render('/privacy', 'Privacy Policy');

        self::assertSame(
            '<a href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>',
            $result
        );
    }

    public function testReversedBracketOrderFallsBackToWholeLabelLink(): void
    {
        $result = $this->render('/privacy', ']a[');

        self::assertSame(
            '<a href="/privacy" target="_blank" rel="noopener noreferrer">]a[</a>',
            $result
        );
    }

    public function testEmptyTrailingSegmentProducesNoTrailingSpace(): void
    {
        $result = $this->render('/privacy', 'Accept [Privacy Policy]');

        self::assertSame(
            'Accept <a href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>',
            $result
        );
    }

    public function testUrlAndLabelAreHtmlEscaped(): void
    {
        $result = $this->render('"><script>alert(1)</script>', 'Click [here]">x');

        self::assertStringNotContainsString('<script>', $result);
        self::assertStringContainsString('&lt;script&gt;', $result);
    }
}
