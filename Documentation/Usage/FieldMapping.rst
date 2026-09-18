.. include:: /Includes.rst.txt

.. _usage-field-mapping:

=============
Field mapping
=============

Every form element gets a custom ``maileonFieldName`` inspector property in
the Form Editor. The extension uses this property to decide what to do with
the submitted value:

- A field named ``email`` (case-insensitive) is always used as the
  contact's email address and is required.
- A field whose ``maileonFieldName`` matches one of Maileon's known
  standard fields is mapped to that standard field:
  ``fullname``, ``lastname``, ``firstname``, ``birthday``, ``address``,
  ``city``, ``country``, ``gender``, ``hnr``, ``locale``, ``nameday``,
  ``organization``, ``region``, ``state``, ``salutation``, ``title``,
  ``zip``.
- Any other field is treated as a **custom field**. If it doesn't exist yet
  in Maileon, the extension creates it automatically, inferring its type
  from the form element type:

  ============== ==============
  Form element   Maileon type
  ============== ==============
  Text            string
  Textarea        string
  Email           string
  Telephone       string
  Url             string
  Number          integer
  Date            date
  Checkbox        boolean
  SingleSelect    string
  Hidden          string
  ============== ==============

Two standard fields are additionally validated before being sent:

- ``gender`` must be one of ``f``, ``m``, ``d`` (case-insensitive).
- ``locale`` must be a two-letter language code (e.g. ``en``, ``de``, ``hu``).
