.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

Install via Composer:

.. code-block:: bash

   composer require xqueue/typo3-maileon-integration

Then activate the extension (this also runs
``vendor/bin/typo3 extension:setup``, which creates the
``tx_typo3maileonintegration_domain_model_xqhbsend`` database table used to
throttle the extension's internal usage-tracking heartbeat).
