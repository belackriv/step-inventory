'use strict';

import _ from 'underscore';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportTableLayoutView.hbs!';
import ReportTableListView from './singleQueryReportTableListView.js';
import PaginationWidgetView from 'lib/common/views/entity/paginationWidgetView.js';
//import ReportTablePaginationView from './singleQueryReportTablePaginationView.js';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(this.collection, 'state:totalRecords:change', this.updateTotalRecords);
  },
  regions: {
    'paginationWidget': '.entity-pagination',
    'tbody': {
      el: 'tbody',
      replaceElement: true
    }
  },
  template: viewTpl,
  ui: {
    'totalRecords': '[data-binding-name="totalRecords"]'
  },
  serializeData(){
    return {
     pagination: { state: this.collection.state },
     columns: this.options.columns
    };
  },
  onRender(){
    this.showChildView('tbody', new ReportTableListView({
      collection: this.collection,
      columns: this.options.columns
    }));
    this.showChildView('paginationWidget', new PaginationWidgetView({
      collection: this.collection,
    }));
    /*
    this.showChildView('tfoot', new ReportTablePaginationView({
      collection: this.collection,
      model: this.model,
      columns: this.options.columns
    }));
    */
  },
  updateTotalRecords(totalRecords){
    this.ui.totalRecords.text(totalRecords);
  }
});