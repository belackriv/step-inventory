"use strict";

import _ from 'underscore';
import jquery from 'jquery';
import JsBarcode from 'jsbarcode';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from  "./travelerIdCardView.hbs!";


export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'printButton': 'button[name="print"]',
    'backButton': 'button[name="back"]',
    'printContainer': 'div[data-ui="printContainer"]',
    'tidCard': '.si-tid-card',
    'barcode': '[jsbarcode-value]',
    'topLabel': 'p[data-ui-top-label]'
  },
  events: {
    'click @ui.printButton': 'print',
    'click @ui.backButton': 'back'
  },
  modelEvents:{
    'change': 'render'
  },
  onRender(){
    this.ui.barcode.each((idx, elem)=>{
      if(elem.getAttribute('jsbarcode-value')){
        JsBarcode(elem, elem.getAttribute('jsbarcode-value'), {
          height:20
        });
      }
    });
  },
  print(event){
    event.preventDefault();
    Radio.channel('app').trigger('print', this.ui.printContainer.html(), {title: 'Sales Item Card'});
  },
  back(event){
    event.preventDefault();
    this.triggerMethod('show:list');
  }
});
