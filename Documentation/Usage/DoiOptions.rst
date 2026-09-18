.. include:: /Includes.rst.txt

.. _usage-doi-options:

============
DOI options
============

The **Maileon Subscribe** finisher exposes the following options in the
Form Editor:

``permission``
   The permission to set on newly created contacts before the DOI process
   (if any) runs. Default: ``none``.

``finalPermission``
   The permission the contact should end up with once opted in. Setting
   this to ``doi+`` requires a valid ``doiKey`` and triggers Maileon's
   "Double Opt-in Plus" process. Default: ``doi+``.

``enableDoiProcess``
   Whether to trigger Maileon's Double Opt-In process at all. Only applies
   when the contact doesn't already exist, or currently has no permission
   set — an existing, already-permissioned contact is updated directly
   without re-triggering DOI. Default: ``false``.

``doiKey``
   The Maileon DOI mailing key to use for the opt-in confirmation email.
   Required when ``enableDoiProcess`` is enabled.

The **Maileon Unsubscribe** finisher has no permission/DOI options — it
always unsubscribes the contact matching the submitted email address.
