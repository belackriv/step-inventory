"use strict";

import _ from 'underscore';
import Marionette from 'marionette';

import ListView from './orderManifestListView.js';
import viewTpl from  "./orderManifestListTableLayoutTpl.hbs!";

import InboundOrderModel from 'lib/accounting/models/inboundOrderModel.js';
import OutboundOrderModel from 'lib/accounting/models/outboundOrderModel.js';

export default Marionette.View.extend({
	template: viewTpl,
	regions: {
	  tbody: {
	    el: 'tbody',
	    replaceElement: true
	  },
	},
	onRender(){
    this.showChildView('tbody', new ListView({
      collection: this.collection
    }));
  },
  serializeData: function(){
  	var data = _.clone(this.model.attributes);
  	data.manifestUrl = this.model.url()+'/manifest';
  	if(this.model instanceof InboundOrderModel){
  		data.type = 'InboundOrder';
  		data.itemName = 'TravelerId';
  	}
  	if(this.model instanceof OutboundOrderModel){
  		data.type = 'OutboundOrder';
  		data.itemName = 'SalesItem';
      data.shippedUrl = this.model.url()+'/shipped';
  	}
    return data;
  },
});