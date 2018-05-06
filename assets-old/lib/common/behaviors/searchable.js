'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

export default Marionette.Behavior.extend({
  initialize(options){
    if(typeof options.selector === 'string'){
      this.ui.searchInput = options.selector;
    }
    this.collectionMode = (typeof this.view.options.usePagination === 'string')?this.view.options.usePagination:'client';
    this.keyUpCooldown = (this.collectionMode === 'server')?500:200;
  },
  ui: {
    'searchInput': '#entity-search-input'
  },
  events: {
    'keyup @ui.searchInput': 'handleSearchInputKeyup',
  },
  handleSearchInputKeyup(event){
    event.preventDefault();
    event.stopPropagation();
    window.clearTimeout(this.searchInputTime);
    this.searchInputTime = window.setTimeout(()=>{
      this.view.triggerMethod('search');
    },this.keyUpCooldown);
  },
  onSearch(){
    this.view.triggerMethod('searchComplete', this.getSearchedCollection());
  },
  getSearchedCollection(){
    let terms = this.ui.searchInput.val().split(' ');
    let collection = this.view.collection;
    let listLength = this.view.options.listLength?this.view.options.listLength:15;

    let filteredCollection =  new BaseUrlBaseCollection([], {
      mode: this.collectionMode,
      queryParams: {
        terms: terms.join(','),
        search: this.getSearchableProperties().join(',')
      },
      state: {
        totalItemCount: collection.length,
        firstPage: 1,
        currentPage: 1,
        pageSize: listLength
      }
    });
    filteredCollection.model = collection.model;
    filteredCollection.url = collection.url;

    if(this.collectionMode === 'server'){
      filteredCollection.fetch();
    }else{
      let searchedCollection = collection;
      for(let term of terms){
        searchedCollection = this.getSearchedCollectionForTerm(term, searchedCollection);
      }
      filteredCollection.reset(searchedCollection.models);
      if(this.options.comparator){
        filteredCollection.comparator = this.options.comparator;
      }
      filteredCollection.state.totalRecords = searchedCollection.models.length;
      filteredCollection.getPage(1);
    }
    return filteredCollection;
  },
  getSearchedCollectionForTerm(term, collection){
    return new Backbone.Collection(collection.filter((model)=>{
        var re = new RegExp(term, 'i');
        return this.testSearchValuesFromModel(re, model);
    }));
  },
  testSearchValuesFromModel(re, model){
    for(let searchValue of this.getSearchValuesFromModel(model)){
      if(re.test(searchValue)){
        return true;
      }
    }
    return false;
  },
  getSearchValuesFromModel(model){
    let searchValues = [];
    if(this.view.options.searchPath){
      let searchPath = this.view.options.searchPath;
      if(typeof searchPath === 'string'){
        searchValues.push(model.getValueFromPath(this.view.options.searchPath));
      }else{
        if(searchPath instanceof Array){
          for(let searchPathElement of searchPath){
            searchValues.push(''+model.getValueFromPath(searchPathElement));
          }
        }
      }
    }else{
      searchValues.push(model.get('name'));
    }
    return searchValues;
  },
  getSearchableProperties(){
    let searchableProperties = [];
    if(this.view.options.searchPath){
      let searchPath = this.view.options.searchPath;
      if(typeof searchPath === 'string'){
        searchableProperties.push(searchPath);
      }else{
        if(searchPath instanceof Array){
          for(let searchPathElement of searchPath){
            searchableProperties.push(''+searchPathElement);
          }
        }
      }
    }
    return searchableProperties;
  }
});