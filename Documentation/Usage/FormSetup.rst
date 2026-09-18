.. include:: /Includes.rst.txt

.. _usage-form-setup:

==========
Form setup
==========

#. Create a form using the TYPO3 Form Editor.
#. Add fields and set the ``maileonFieldName`` property on each field that
   should be sent to Maileon (see :ref:`usage-field-mapping`).
#. Add either the **Maileon Subscribe** or **Maileon Unsubscribe** finisher
   at the end of the form, depending on the form's purpose.
#. Configure the finisher options — permission, DOI settings (see
   :ref:`usage-doi-options`).
#. Save and include the form on any page.

An example form definition with a working subscribe setup ships with the
extension at ``Resources/Private/Forms/maileonSubscribeForm.form.yaml`` and
can be used as a starting point.
