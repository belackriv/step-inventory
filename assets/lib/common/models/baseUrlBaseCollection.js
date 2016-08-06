'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import 'backbone.paginator';

const proto = Backbone.PageableCollection.prototype;

export default Backbone.PageableCollection.extend({
  //baseUrl: '/~belac/stepthrough/app_dev.php',
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