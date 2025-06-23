<?php

namespace XQueue\Typo3MaileonIntegration\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class LinkLabelViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('url', 'string', 'The desired URL to link to', true);
        $this->registerArgument('label', 'string', 'The label used for the link', true);
    }

    public function render(): string
    {
        $url = $this->arguments['url'];
        $label = $this->arguments['label'];

        $indexStart = strpos($label, '[');
        $indexEnd = strpos($label, ']');

        if ($indexStart === false || $indexEnd === false || $indexStart > $indexEnd) {
            return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($label) . '</a>';
        }

        $start = trim(substr($label, 0, $indexStart));
        $linkText = trim(substr($label, $indexStart + 1, $indexEnd - $indexStart - 1));
        $end = trim(substr($label, $indexEnd + 1));

        return htmlspecialchars($start) .
            ' <a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($linkText) . '</a>' .
            (strlen($end) ? ' ' . htmlspecialchars($end) : '');
    }
}