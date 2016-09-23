'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Syphon from 'backbone.syphon';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportFormLayoutView.hbs!';
import ReportFormListView from './singleQueryReportFormListView.js';

export default Marionette.View.extend({
  initialize(){
    Radio.channel('reporting').reply('singleQueryReport:queryParams', this.getQueryParams.bind(this));
  },
  regions: {
    'parameters': '[data-region-name="parameters"]'
  },
  ui:{
    'form': 'form'
  },
  template: viewTpl,
  onRender(){
    let collection = new Backbone.Collection(
      this.model.get('singleQueryReportParameters').where({isHidden: false})
    );
    this.showChildView('parameters', new ReportFormListView({
      collection: collection
    }));
  },
  getQueryParams(){
    return Syphon.serialize(this);
  }
});