'use strict';

import $ from 'jquery';
import _ from 'underscore';
import Marionette from 'marionette';
import viewTpl from './paginationWidgetView.hbs!';

export default Marionette.View.extend({
  initialize(options){
    this.listenTo(this.collection, 'pageable:state:change', this.stateChanged);
    this.listenTo(this.collection, 'update', this.stateChanged);
    this.isFetching = false;
  },
  template: viewTpl,
  serializeData(){
	 let data = {
      isFetching: this.isFetching,
      shownItemCount: this.collection.length,
      totalItemCount: this.collection.state.totalItemCount,
      matchedItemCount: this.collection.state.totalRecords,
     	state: this.collection.state,
     	hasPreviousPage: this.collection.hasPreviousPage(),
		  hasNextPage: this.collection.hasNextPage(),
		  pageButtons: this.getPageButtons()
    };
    return data;
  },
  ui: {
    'firstPage' : '[data-ui-name="paginationFirst"]',
    'prevPage' : '[data-ui-name="paginationPrev"]',
    'nextPage' : '[data-ui-name="paginationNext"]',
    'lastPage' : '[data-ui-name="paginationLast"]',
    'getPage' : '[data-ui-name="paginationPage"]'
  },
  events: {
    "click @ui.firstPage": "getFirstPage",
    "click @ui.prevPage": "getPrevPage",
    "click @ui.nextPage": "getNextPage",
    "click @ui.lastPage": "getLastPage",
    "click @ui.getPage": "getPage"
  },
  stateChanged(){
    this.render();
  },
  getPageButtons(){
  	let currentPage = this.collection.state.currentPage;
  	let firstPage = this.collection.state.firstPage;
  	let lastPage = this.collection.state.lastPage;
  	let pagesStart = (currentPage - 3 > firstPage)?currentPage - 3:firstPage;
  	let pagesEnd = (currentPage + 3 < lastPage)?currentPage + 3:lastPage;
  	let pageButtons = [];
  	for(let page = pagesStart; page <= pagesEnd; page++){
  		let isActive = (page === currentPage)?true:false;
  		pageButtons.push({
  			page: page,
  			active: isActive,
  			label: page
  		});
  	}
  	return pageButtons;
  },
  getFirstPage: function(event){
    this.isFetching = true;
  	this.collection.getFirstPage().always(()=>{
      this.isFetching = false;
      this.render();
    });
    this.render();
  },
  getPrevPage: function(event){
    this.isFetching = true;
  	this.collection.getPreviousPage().always(()=>{
      this.isFetching = false;
      this.render();
    });
    this.render();
  },
  getNextPage: function(event){
    this.isFetching = true;
   	this.collection.getNextPage().always(()=>{
      this.isFetching = false;
      this.render();
    });
    this.render();
  },
  getLastPage: function(event){
    this.isFetching = true;
  	this.collection.getLastPage().always(()=>{
      this.isFetching = false;
      this.render();
    });
    this.render();
  },
  getPage: function(event){
    this.isFetching = true;
    let page = parseInt($(event.currentTarget).attr('page'));
    this.collection.getPage(page).always(()=>{
      this.isFetching = false;
      this.render();
    });;
    this.render();
  },
});