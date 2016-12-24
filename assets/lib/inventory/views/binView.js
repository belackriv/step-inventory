"use strict";

import _ from 'underscore';
import jquery from 'jquery';
import JsBarcode from 'jsbarcode';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from  "./binView.hbs!";


export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'printButton': 'button[name="print"]',
    'backButton': 'button[name="back"]',
    'printContainer': 'div[data-ui="printContainer"]',
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
  serializeData(){
    let data = _.clone(this.model.attributes);
    data.tidColumns = [[],[],[]];
    this.model.get('travelerIds').each((tid, index)=>{
      let columnIndex = index % data.tidColumns.length;
      data.tidColumns[columnIndex].push(tid);
    });
    return data;
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
  onAttach(){
    this.resizeTopLabels();
  },
  onDomRefresh(){
    this.resizeTopLabels();
  },
  resizeTopLabels(){
    this.ui.topLabel.each((idx, elem)=>{
      let width = jquery(elem).siblings('svg').first().width();
      jquery(elem).width(width);
    });
  },
  print(event){
    event.preventDefault();
    Radio.channel('app').trigger('print', this.ui.printContainer.html(), {title: 'Bin Sheet'});
  },
  back(event){
    event.preventDefault();
    this.triggerMethod('show:list');
  }
});
