'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import 'backbone.paginator';

const proto = Backbone.PageableCollection.prototype;
const PageableCollection = Backbone.PageableCollection.extend({
  //baseUrl: '/~belac/step-inventory/app_dev.php',
  baseUrl: '',
  fetch(options){
    Radio.channel('app').trigger('request:started');
    return proto.fetch.call(this, options).always(()=>{
      Radio.channel('app').trigger('request:finished');
    });
  },
  parseState: function (resp, queryParams, state, options){
    if(this.state.totalRecords !== resp.total_count){
      this.trigger('state:totalRecords:change', resp.total_count);
    }
    return {totalRecords: resp.total_count, totalItemCount: resp.total_items};
  },
  parseRecords: function (resp, options){
    return resp.list;
  }
});

PageableCollection.prototype.__prepareModel = PageableCollection.prototype._prepareModel;
PageableCollection.prototype._prepareModel  = Backbone.Relational.Collection.prototype._prepareModel;

export default PageableCollection;