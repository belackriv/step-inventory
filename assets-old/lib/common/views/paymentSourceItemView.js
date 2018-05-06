'use strict';

import Marionette from 'marionette';
import viewTpl from './paymentSourceItemView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
  ui:{
  	'removePaymentMethodButton': '[data-ui="removePaymentMethod"]'
  },
  events:{
  	'click @ui.removePaymentMethodButton': 'removePaymentMethod'
  },
  removePaymentMethod(event){
  	event.preventDefault();
    if(this.ui.removePaymentMethodButton.data('confirmed') === true){
      this.ui.removePaymentMethodButton.addClass('is-loading').prop('disabled', true);
  		this.model.destroy();
    }else{
      this.ui.removePaymentMethodButton.data('confirmed', true).text('Confirm?');
    }
  }
});
