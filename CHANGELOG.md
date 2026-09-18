# 4.2.0
## 2026.09.15
- Removed the duplicated, outdated Maileon API client copy bundled under `Resources/Private/Contrib`, which shadowed the real Composer dependency under the same namespace; relied solely on the Composer-managed package as an interim step.
- Removed the `xqueue/maileon-api-client` Composer dependency entirely. The handful of API calls actually used (contact lookup/create, unsubscribe, custom fields, ping, account info) are now made directly via TYPO3's own HTTP client, so the extension can be installed without Composer again.
- Cache the Maileon API key validation instead of firing 3 blocking HTTP pings on every form submission.
- Fixed `MaileonSubscribeFinisher`/`MaileonUnsubscribeFinisher` to properly surface `FinisherException` instead of a generic `Exception`; added logging and `isSuccess()` checks throughout `FormProcessingService`.
- Fixed a class-name casing bug in `XQHbSendRepository` and migrated the deprecated TCA `eval="int"` field to TYPO3 14's `type="number"`.
- Added a PHPUnit unit/functional test suite, PHPStan static analysis, and a GitHub Actions CI pipeline.

# 4.1.0
## 2025.12.19
- Added functionality, it can be set what the final permission should be in the case of a DOI process.

# 4.0.1
## 2025.08.28
- Fixed error in XQHbSendRepository related to persistenceManager property.

# 4.0.0
## 2025.06.19

- Refactored the extension to use the TYPO3 Form Framework instead of the previous custom form handling.
- Support for subscribing and unsubscribing contacts via TYPO3 Forms.
- Admin UI configuration via Extension Settings (API key, etc.).
- New form finisher: `MaileonSubscribeFinisher` and `MaileonUnsubscribeFinisher`.
- Support for mapping form fields to Maileon standard and custom fields.
- Automatic creation of Maileon custom fields if they don’t exist.
- Configurable DOI (Double Opt-in) support.

# 3.2.1
## 2025.02.11

- The subscription form can be customised
- Custom fields can be added to the subscription form
- Add XSIC Heartbeat functionality

# 3.2.0
## 2024.11.27

- Extension compatible with Typo3 v13 LTS

# 3.1.0
## 2023.05.09

- Extension compatible with Typo3 v12 LTS

# 3.0.1
## 2023.04.20

- Change logo, descriptions

# 3.0.0
## 2023.04.10

- Added composer
- Changed Maileon PHP API library to composer version
- Extension compatible with Typo3 v11 LTS

# 2.5.1
## 2021-04-22

- Added configuration parameter for target permission (doi or doi+)

# 2.5.0
## 2020-12-11

- Refactor code to TYPO3 coding guidelines
- Remove forms dependency
- Don't save any data in TYPO3 database anymore

# 1.0.0
## 2015-10-05

- Initial code generated with kickstarter
