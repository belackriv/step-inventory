'use strict';

import _ from 'underscore';
import Marionette from 'marionette';
import viewTpl from './singleQueryReportTableRowView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
  serializeData(){
    var data =  {};
    data.data = _.clone(this.model.attributes);
    data.columns = this.options.columns;
    return data;
  },
});