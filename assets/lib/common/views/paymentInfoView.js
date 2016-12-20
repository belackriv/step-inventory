"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import Syphon from 'backbone.syphon';

import viewTpl from './paymentInfoView.hbs!';

import PaymentSourceCardModel from '../models/paymentCardSourceModel.js';

export default Marionette.View.extend({
  initialize(){

  },
  template: viewTpl,
  regions:{
/*
    'billingHistory': {
      el: 'tbody[data-region="billingHistory"]',
      replaceElement: true
    }
*/
  },
  ui: {
    'form': 'form',
    'addButton': '[data-ui-name="add"]',
    'cancelButton': '[data-ui-name="cancel"]'
  },
  events:{
    'submit @ui.form': 'addPaymentMethod',
    'click @ui.cancelButton': 'cancel',
  },
  onAttach(){
    if(!window.Stripe){
      this.$el.append('<script src="https://js.stripe.com/v2/"></script>');
    }
  },
  cancel(event){
    event.preventDefault();
    Radio.channel('dialog').trigger('close');
  },
  addPaymentMethod(event){
    event.preventDefault();
    this.disableButtons();
    Stripe.setPublishableKey(this.model.get('stripePublicKey'));
    let data = Syphon.serialize(this);
    Stripe.card.createToken(data, (respCode, stripeData)=>{
      let paymentSource = PaymentSourceCardModel.findOrCreate({
        externalId: stripeData.id,
        account: this.model
      });
      paymentSource.save().then(()=>{
        this.enableButtons();
      });
    });
  },
  disableButtons(){
    this.ui.addButton.addClass('is-loading').prop('disabled', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disabled', true);
  },
  enableButtons(){
    this.ui.addButton.removeClass('is-loading').prop('disabled', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disabled', false);
  }
});