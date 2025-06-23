# Typo3 - Maileon Integration Package

This extension provides seamless integration between TYPO3 Forms and [Maileon](https://www.maileon.com/), a powerful email marketing platform. It enables automatic contact synchronization from frontend forms, including support for custom fields, DOI processes, and subscription/unsubscription workflows.

## Features

- ✅ Integrates with TYPO3's `form` extension
- ✅ Map form fields to Maileon standard or custom fields
- ✅ Automatically create missing custom fields in Maileon
- ✅ Handles Single Opt-In, Confirmed Opt-In, and Double Opt-In flows
- ✅ Sends subscription and unsubscription requests via the Maileon API
- ✅ Configurable via Extension Settings (Admin UI)
- ✅ Supports Finisher presets in the form editor

## Requirements

- TYPO3 v12.4 or higher
- PHP 8.1+
- TYPO3 `form` system extension
- A valid Maileon API key

## Minimal Dependencies

* TYPO3 CMS 12.4.x or 13.4.x for Typo3 - Maileon Integration Package 3.2.x
* TYPO3 CMS 11.5.0 or 12.4.99 for Typo3 - Maileon Integration Package 3.1.x
* TYPO3 CMS 10.4 or 11.4.99 for Typo3 - Maileon Integration Package 3.x

## Installation

Install via Composer:

```bash
composer require xqueue/typo3-maileon-integration
```
Activate the extension in the TYPO3 Extension Manager.

## Configuration

1. Go to **Admin Tools → Settings → Extension Configuration**.
2. Locate **TYPO3 Maileon Integration** and set:
    - Your **Maileon API Key**

## Usage

1. Create a form using the **TYPO3 Form Editor**.
2. Add fields and set the `maileonFieldName` in each field’s.
3. Add the **Maileon Finisher** at the end of the form.
4. Configure the finisher options (e.g., permission, DOI settings).
5. Save and include the form on any page.

## Standard vs Custom Fields

The extension automatically categorizes fields based on the `maileonFieldName`:

- **Standard fields**: `firstname`, `lastname`, `locale`, etc.
- **Custom fields**: Any field not in the standard list will be created in Maileon if it doesn’t exist.

## Documentation

* [User Documentation](https://xqueue.atlassian.net/wiki/spaces/MSI/pages/482672647/Typo3+Form+version+v4.0)
