'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import ChildView from './singleQueryReportFormItemView.js';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  //className: 'si-single-query-report-form'
  className: 'is-flex'
});
