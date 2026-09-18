<?php

/**
 * Copied from typo3/testing-framework's Resources/Core/Build/FunctionalTestsBootstrap.php
 * boilerplate, as recommended by that file's own docblock. Keep in sync when
 * bumping the typo3/testing-framework dependency.
 */
(static function () {
    $testbase = new \TYPO3\TestingFramework\Core\Testbase();
    $testbase->defineOriginalRootPath();
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/tests');
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/transient');
})();
