CREATE TABLE tx_typo3maileonintegration_domain_model_xqhbsend (
    uid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pid INT(10) UNSIGNED DEFAULT 0,
    task VARCHAR(255) NOT NULL DEFAULT '',
    last_execution INT(10) UNSIGNED DEFAULT 0
);
