# Typo3 - Maileon Integration Package

![CI](https://github.com/xqueue/typo3-maileon-integration/actions/workflows/ci.yml/badge.svg)

This extension provides seamless integration between TYPO3 Forms and [Maileon](https://www.maileon.com/), a powerful email marketing platform. It enables automatic contact synchronization from frontend forms, including support for custom fields, DOI processes, and subscription/unsubscription workflows.

## Requirements

- TYPO3 v12.4, v13.4 or v14.3
- PHP 8.1+
- TYPO3 `form` system extension
- A valid Maileon API key

## Installation

Install via Composer:

```bash
composer require xqueue/typo3-maileon-integration
```
Activate the extension in the TYPO3 Extension Manager.

## Documentation

Full documentation — features, configuration, form setup, field mapping and
DOI options — lives in [`Documentation/Index.rst`](Documentation/Index.rst).
