<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class XQHbSend extends AbstractEntity
{
    protected string $task = '';
    protected int $lastExecution = 0;

    public function getTask(): string
    {
        return $this->task;
    }

    public function setTask(string $task): void
    {
        $this->task = $task;
    }

    public function getLastExecution(): int
    {
        return $this->lastExecution;
    }

    public function setLastExecution(int $lastExecution): void
    {
        $this->lastExecution = $lastExecution;
    }
}