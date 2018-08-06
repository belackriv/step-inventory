'use strict';

import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportTableRowView.hbs!';

import LoadingView from 'lib/common/views/loadingView.js';
import OutboundOrderCollection from  'lib/accounting/models/outboundOrderCollection.js';
import InboundOrderCollection from  'lib/accounting/models/inboundOrderCollection.js';
import OrderManifestListTableLayoutView from 'lib/admin/views/orderManifestListTableLayoutView.js';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
  serializeData(){
    var data =  {};
    data.data = _.clone(this.model.attributes);
    data.columns = this.options.columns;
    return data;
  },
  ui:{
    'actionItem': '[data-ui-action]',
  },
  events: {
    'click @ui.actionItem': 'doAction',
  },
  doAction(event){
	if(typeof this[event.target.dataset.uiAction] === 'function'){
		this[event.target.dataset.uiAction](event);
	}
  },
  showOutboundManifest(event){
    event.preventDefault();
    let outboundOrderCollection = Radio.channel('data').request('collection', OutboundOrderCollection, {doFetch: false});
    const outboundOrderModel = outboundOrderCollection.model.findOrCreate({id: event.target.dataset.id});
    let options = {
      title: 'OutboundOrder Manifest',
      width: '800px'
    };
    let view = new OrderManifestListTableLayoutView({
      collection: outboundOrderModel.get('salesItems'),
      model: outboundOrderModel,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', new LoadingView(), options);
    outboundOrderModel.fetch().then(()=>{
      Radio.channel('dialog').trigger('open', view, options);
    });
  },
  showInboundManifest(event){
    event.preventDefault();
    let inboundOrderCollection = Radio.channel('data').request('collection', InboundOrderCollection, {doFetch: false});
    const inboundOrderModel = inboundOrderCollection.model.findOrCreate({id: event.target.dataset.id});
    let options = {
      title: 'OutboundOrder Manifest',
      width: '800px'
    };
    let view = new OrderManifestListTableLayoutView({
      collection: inboundOrderModel.get('travelerIds'),
      model: inboundOrderModel,
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', new LoadingView(), options);
    inboundOrderModel.fetch().then(()=>{
      Radio.channel('dialog').trigger('open', view, options);
    });
  }
});