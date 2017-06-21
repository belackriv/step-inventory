'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import 'backbone.paginator';

const proto = Backbone.PageableCollection.prototype;
const PageableCollection = Backbone.PageableCollection.extend({
   url(){
    return 'url';
  }
});

PageableCollection.prototype.__prepareModel = PageableCollection.prototype._prepareModel;
PageableCollection.prototype._prepareModel  = Backbone.Relational.Collection.prototype._prepareModel;

export default PageableCollection;