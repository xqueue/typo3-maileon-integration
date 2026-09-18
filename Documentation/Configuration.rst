.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

The extension has a single global setting, managed through TYPO3's
Extension Configuration API:

#. Go to :guilabel:`Admin Tools > Settings > Extension Configuration`.
#. Locate :guilabel:`typo3_maileon_integration` and set:

   - **Maileon API key** — your account's API key, found in your Maileon
     account under API access. This key is used for every request made to
     the Maileon API (`https://api.maileon.com/1.0`), including contact
     creation, unsubscription, and the periodic key-validity check.

The validity of the configured API key is checked with the Maileon API and
cached for 10 minutes (cache identifier ``maileon_api_validation``) to avoid
adding latency to every form submission. If you rotate the API key, the new
key is picked up automatically — the cache is keyed by the key's own hash.
