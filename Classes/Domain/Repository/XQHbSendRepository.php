<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use XQueue\Typo3MaileonIntegration\Domain\Model\XQHbSend;

class XQHbSendRepository extends Repository
{
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }
    public function findByTask(string $task): ?XqHbSend
    {
        return $this->findOneBy(['task' => $task]);
    }

    /**
     */
    public function hasTaskRunToday(string $task): bool
    {
        $record = $this->findByTask($task);

        if (!$record) {
            return false;
        }

        $today = (new \DateTime('today'))->getTimestamp();
        return $record->getLastExecution() >= $today;
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    public function updateLastExecution(string $task): void
    {
        $record = $this->findByTask($task);

        if ($record === null) {
            $record = new XqHbSend();
            $record->setTask($task);
            $record->setLastExecution(time());

            $this->add($record);
        } else {
            $record->setTask($task);
            $record->setLastExecution(time());

            $this->update($record);
        }
    }
}