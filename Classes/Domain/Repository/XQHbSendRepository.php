<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Repository;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class XQHbSendRepository extends Repository
{
    protected string $tableName = 'tx_typo3maileonintegration_domain_model_xqhbsend';
    public function findByTask(string $task)
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('task', $task))->execute()->getFirst();
    }

    /**
     * @throws InvalidQueryException
     */
    public function hasTaskRunToday(string $task): bool
    {
        $startOfDay = strtotime('today midnight');

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('task', $task),
                $query->greaterThanOrEqual('lastExecution', $startOfDay)
            )
        );

        $result = $query->execute()->getFirst();

        return $result !== null;
    }

    public function updateLastExecution(string $task): void
    {
        $currentTimestamp = time();
        $existingRecord = $this->findByTask($task);

        // Get the database connection
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->tableName)
            ->createQueryBuilder();

        if ($existingRecord) {
            // Update existing record
            $queryBuilder
                ->update($this->tableName)
                ->set('last_execution', $currentTimestamp)
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(
                    $existingRecord->getUid(),
                    ParameterType::INTEGER
                )))
                ->executeStatement();
        } else {
            // Insert new record
            $queryBuilder
                ->insert($this->tableName)
                ->values([
                    'pid' => 0, // Set PID to 0 for frontend records
                    'task' => $task,
                    'last_execution' => $currentTimestamp,
                ])
                ->executeStatement();
        }
    }
}