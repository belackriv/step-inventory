"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminMenuLinksEditView.hbs!";
import FormChildListView from 'lib/common/views/formChildListView.js';
import routeMatchItemViewTpl from './routeMatchItemView.hbs!';

export default Marionette.View.extend({
  initialize(){
    this.routeMatchesCollection = new Backbone.Collection();
    this.routeMatchesChanged();
  },
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
  },
  regions:{
    'routeMatches': '[data-ui-name="routeMatches"]'
  },
  ui: {
    'nameInput': 'input[name="name"]',
    'urlInput': 'input[name="url"]',
    'routeMatchInput': 'input[name="routeMatch"]',
    'addRouteMatchButton': 'button[name="addRouteMatch"]',
  },
  events: {
    'click @ui.addRouteMatchButton': 'onAddRouteMatchButtonClicked'
  },
  modelEvents:{
    'change:routeMatches': 'routeMatchesChanged',
  },
  bindings: {
    '@ui.nameInput': 'name',
    '@ui.urlInput': 'url',
  },
  onRender(){
   this.showChildView('routeMatches', new FormChildListView({
      collection: this.routeMatchesCollection,
      childTemplate: routeMatchItemViewTpl
    }));
  },
  routeMatchesChanged(){
    _.each(this.model.get('routeMatches'),(routeMatch)=>{
      this.createAndAddRouteMatchModel(routeMatch);
    });
  },
  createAndAddRouteMatchModel(routeMatch){
    let model = new Backbone.Model({
      label: routeMatch
    });
    this.listenTo(model, 'destroy', ()=>{
      let index = this.model.get('routeMatches').indexOf(model.get('label'));
      if(index > -1){
        this.model.get('routeMatches').splice(index, 1);
      }
    });
    this.routeMatchesCollection.add(model);
  },
  onAddRouteMatchButtonClicked(event){
    event.preventDefault();
    let routeMatch = this.ui.routeMatchInput.val().trim();
    if(this.model.get('routeMatches').indexOf(routeMatch) < 0){
      this.model.get('routeMatches').push(routeMatch);
      this.createAndAddRouteMatchModel(routeMatch);
    }
  }
});
