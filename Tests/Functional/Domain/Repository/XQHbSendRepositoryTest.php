<?php

namespace XQueue\Typo3MaileonIntegration\Tests\Functional\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use XQueue\Typo3MaileonIntegration\Domain\Repository\XQHbSendRepository;

/**
 * Regression guard for the injectPersistenceManager fix in v4.0.1
 * (see CHANGELOG.md) — a repository that can't be constructed/persisted
 * correctly would fail every test in this case.
 */
class XQHbSendRepositoryTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['form'];
    protected array $testExtensionsToLoad = ['typo3_maileon_integration'];

    protected XQHbSendRepository $subject;
    protected PersistenceManagerInterface $persistenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/xq_hb_send.csv');
        $this->subject = $this->get(XQHbSendRepository::class);
        $this->persistenceManager = $this->get(PersistenceManagerInterface::class);
    }

    public function testFindByTaskReturnsNullForUnknownTask(): void
    {
        self::assertNull($this->subject->findByTask('unknown_task'));
    }

    public function testFindByTaskReturnsMatchingEntity(): void
    {
        $record = $this->subject->findByTask('past_task');

        self::assertNotNull($record);
        self::assertSame('past_task', $record->getTask());
        self::assertSame(1577836800, $record->getLastExecution());
    }

    public function testHasTaskRunTodayIsFalseForUnknownTask(): void
    {
        self::assertFalse($this->subject->hasTaskRunToday('unknown_task'));
    }

    public function testHasTaskRunTodayIsFalseForPastTimestamp(): void
    {
        self::assertFalse($this->subject->hasTaskRunToday('past_task'));
    }

    public function testUpdateLastExecutionCreatesRecordWhenNoneExists(): void
    {
        $this->subject->updateLastExecution('new_task');
        $this->persistenceManager->persistAll();

        self::assertTrue($this->subject->hasTaskRunToday('new_task'));
    }

    public function testUpdateLastExecutionUpdatesExistingRecordWithoutDuplicating(): void
    {
        $this->subject->updateLastExecution('past_task');
        $this->persistenceManager->persistAll();

        self::assertTrue($this->subject->hasTaskRunToday('past_task'));

        $connection = $this->getConnectionPool()->getConnectionForTable('tx_typo3maileonintegration_domain_model_xqhbsend');
        $count = $connection->count('uid', 'tx_typo3maileonintegration_domain_model_xqhbsend', ['task' => 'past_task']);
        self::assertSame(1, $count);
    }
}
