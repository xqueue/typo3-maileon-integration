.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _what-it-does:

What does it do?
=================

This extension provides seamless integration between TYPO3 Forms and
`Maileon <https://www.maileon.com/>`__. It enables automatic contact
synchronization from frontend forms, including support for custom fields,
DOI processes, and subscription/unsubscription workflows.

.. _features:

Features
========

- Integrates with TYPO3's ``typo3/cms-form`` extension
- Maps form fields to Maileon standard or custom fields
- Automatically creates missing custom fields in Maileon
- Handles Single Opt-In, Confirmed Opt-In, and Double Opt-In flows
- Sends subscription and unsubscription requests via the Maileon API
- Configurable via Extension Settings (Admin UI)
- Supports Finisher presets in the form editor

.. _requirements:

Requirements
============

- TYPO3 v12.4, v13.4 or v14.3
- PHP 8.1+
- The TYPO3 ``typo3/cms-form`` system extension
- A valid Maileon API key
