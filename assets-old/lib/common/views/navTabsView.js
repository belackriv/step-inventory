"use strict";

import Radio from 'backbone.radio';
import Marionette from 'marionette';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'route:changed', this.render);
    this.listenTo(Radio.channel('data').request('myself'), 'update:userRoles', this.render);
    this.listenTo(Radio.channel('data').request('myself'), 'change:userRoles', this.render);
  },
  className(){
  	let classes = 'tabs is-toggle';
  	if(this.options.hasDropdown){
  		classes += ' has-dropdown';
  	}
  	return classes;
  },
  getTemplate(){
    return this.options.template;
  },
});