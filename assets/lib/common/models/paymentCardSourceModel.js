'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import PaymentSourceModel from './paymentSourceModel.js';

let Model = PaymentSourceModel.extend({
  defaults: {
    brand: null,
    last4: null,
    expirationMonth: null,
    expirationYear: null,
  },
});

globalNamespace.Models.PaymentCardSourceModel = Model;

export default Model;