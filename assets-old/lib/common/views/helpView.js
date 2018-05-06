"use strict";

import util from 'lib/util.js';
import jquery from 'jquery';
import _ from 'underscore';
import Marionette from 'marionette';

import viewTpl from './helpView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  className: 'panel si-help-panel',
  onAttach(){
  	this.$el.find('.accordion').accordion(this.getAccordionOptions());
  	this.moveArrowsToLevelLeft();
  },
  getAccordionOptions(){
  	return this.$el.find('.accordion').data();
  	/*
  	let options = this.$el.find('.accordion').data();
  	let castedOptions = {};
  	_.each(options, (value, key)=>{
  		if(key === 'active' && value === 'false'){
  			castedOptions[key] = false;
  		}else{
  			castedOptions[key] = util.castAsType(value, this.accordionOptionTypes[key]);
  		}
  	});
  	return castedOptions;
  	*/
  },
  //this moves the arrows from the accordion to the level left item if it exists
  moveArrowsToLevelLeft(){
  	this.$el.find('.accordion').find('.level').each((ind, elem)=>{
  		jquery(elem).find('.level-left').prepend(jquery(elem).siblings());
  	});
  },
  accordionOptionTypes: {
  	active: 'integer',
  	collapsible: 'boolean',
  	heightStyle: 'string'
  }
});