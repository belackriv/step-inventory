'use strict';

import viewTpl from './searchableListItemView.hbs!';
import BaseItemView from './searchableListBaseItemView';

export default BaseItemView.extend({
  template: viewTpl,
  tagName: 'li',
});