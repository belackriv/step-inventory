"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import Syphon from 'backbone.syphon';

import viewTpl from './paymentInfoView.hbs!';

import PaymentSourceCardModel from '../models/paymentCardSourceModel.js';

export default Marionette.View.extend({
  stripe: null,
  stripElements: null,
  template: viewTpl,
  ui: {
    'form': 'form',
    'stripeElement': '[data-ui-name="stripeElement"]',
    'stripeError': '[data-ui-name="stripeError"]',
    'addButton': '[data-ui-name="add"]',
    'cancelButton': '[data-ui-name="cancel"]'
  },
  events:{
    'submit @ui.form': 'addPaymentMethod',
    'click @ui.cancelButton': 'cancel',
  },
  onRender(){
    if(!Stripe){
      alert('waiting on stripe...');
    }else{
      this.initializeStripeElement();
    }
  },
  initializeStripeElement(){
    const stripe = Stripe(this.model.get('stripePublicKey'));
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount(this.ui.stripeElement.get(0));
    card.addEventListener('change', (event)=>{
      if(event.error){
        this.ui.stripeError.text(event.error.message).show();
      } else {
        this.ui.stripeError.text('').hide();
      }
    });
    this.ui.stripeError.hide();
    this.stripeCard = card;
    this.stripe = stripe;
  },
  cancel(event){
    event.preventDefault();
    Radio.channel('dialog').trigger('close');
  },
  addPaymentMethod(event){
    event.preventDefault();
    this.disableButtons();
    this.stripe.createSource(this.stripeCard).then((result)=>{
      if(result.error){
        alert(result.error);
      }else{
        let paymentSource = PaymentSourceCardModel.findOrCreate({
          externalId: result.source.id,
          account: this.model
        });
        paymentSource.save().then(()=>{
          this.enableButtons();
          Radio.channel('dialog').trigger('close');
        });
      }
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