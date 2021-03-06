"use strict";

import _ from 'underscore';
import jquery from 'jquery';
import JsBarcode from 'jsbarcode';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from  "./inboundOrderView.hbs!";


export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'toggleViewTypeButton': 'button[name="toggleViewType"]',
    'printButton': 'button[name="print"]',
    'backButton': 'button[name="back"]',
    'printContainer': 'div[data-ui="printContainer"]',
    'barcode': '[jsbarcode-value]',
    'topLabel': 'p[data-ui-top-label]'
  },
  events: {
    'click @ui.toggleViewTypeButton': 'toggleViewType',
    'click @ui.printButton': 'print',
    'click @ui.backButton': 'back'
  },
  modelEvents:{
    'change': 'render'
  },
  showCompactView: false,
  serializeData(){
    let data = _.clone(this.model.attributes);
    data.showCompactView = this.showCompactView;
    if(!this.showCompactView){
      data.tidColumns = [[],[],[]];
      data.travelerIdCount = 0;
      let columnIndex = 0;
      this.model.get('travelerIds').each((tid, index)=>{
        if(!tid.get('transform') && !tid.get('isVoid')){
          data.tidColumns[columnIndex].push(tid);
          columnIndex = ((columnIndex + 1) >= data.tidColumns.length)?0:columnIndex + 1;
          data.travelerIdCount++;
        }
      });
    }
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
  toggleViewType(){
    this.showCompactView = !this.showCompactView;
    this.render();
  },
  print(event){
    event.preventDefault();
    Radio.channel('app').trigger('print', this.ui.printContainer.html(), {title: 'Inbound Order Sheet'});
  },
  back(event){
    event.preventDefault();
    this.triggerMethod('show:list');
  }
});
