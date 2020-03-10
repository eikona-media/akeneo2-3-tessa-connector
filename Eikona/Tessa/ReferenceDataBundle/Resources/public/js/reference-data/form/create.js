/*
 * create.js
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

'use strict';

define(
  [
    'jquery',
    'underscore',
    'backbone',
    'routing',
    'pim/form-builder',
    'pim/user-context',
    'oro/translator',
    'oro/loading-mask',
    'oro/navigation',
    'eikona/tessa/connector/reference-data/form/field'
  ],
  function (
    $,
    _,
    Backbone,
    Routing,
    FormBuilder,
    UserContext,
    __,
    LoadingMask,
    Navigation,
    TessaReferenceDataFormField
  ) {
    return {
      createNewTessaFormField (el, value, options) {
        const field = new TessaReferenceDataFormField({el});
        field.setOptions(options);
        return field.render();
      }
    };
  }
);
