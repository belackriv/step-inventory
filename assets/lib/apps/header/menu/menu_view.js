import StepThrough from 'lib/app';
import menuLayoutTpl from './templates/menu_layout.hbs!';
import menuSelectionTpl from './templates/menu_selection.hbs!';
import menuDepartmentSelectionTpl from './templates/menu_department_selection.hbs!';
import menuHeaderTpl from './templates/menu_header.hbs!';

StepThrough.module("HeaderApp.Menu.View", function(View, StepThrough, Backbone, Marionette, $, _){

  View.MenuHeader = Marionette.ItemView.extend({
    template: menuHeaderTpl,
    tagName: "li",
    className: "dropdown",
    events: {
      "click a": "navigate"
    },
    navigate: function(e){
      e.preventDefault();
      var menuLink = this.model.get('menuLink');
      if(menuLink.appTrigger){
        StepThrough.trigger(menuLink.appTrigger);
      }
    },
    templateHelpers: function () {
      return {
        baseURL: StepThrough.baseURL
      };
    },
    onRender: function(){
      if(this.model.get('children').length > 0){
        this.$el.find('.dropdown-toggle').dropdownHover({
          delay: 0,
          instantlyCloseOthers: true
        });
      }
    }
  });

  View.Menu = Marionette.CollectionView.extend({
    childView: View.MenuHeader,
    tagName: "ul",
    className: "nav navbar-nav"
  });

  View.DepartmentSelection = Marionette.ItemView.extend({
    initialize : function (options) {
      this.menuLayoutView = options.menuLayoutView;
    },
    tagName: 'select',
    attributes: {
      id: 'menu-department-select'
    },
    template: menuDepartmentSelectionTpl,
    events: {
      "change": "changeDepartment"
    },
    changeDepartment: function(){
      var departmentId = this.$el.val();
      var menuItems = this.collection.get(departmentId).get('menuItems');
      var menuItemsCollection = new StepThrough.Entities.MenuItemCollection(menuItems);
      var menuView = new View.Menu({
        collection: menuItemsCollection
      });
      this.menuLayoutView.getRegion('menu').show(menuView);
    },
    onShow: function() {
      this.changeDepartment();
    }
  });

  View.MenuSelection = Marionette.LayoutView.extend({
    initialize : function (options) {
      this.menuLayoutView = options.menuLayoutView;
    },
    template: menuSelectionTpl,
    regions: {
      departmentSelection: "#department-selection-container",
    },
    ui: {
      officeSelect: "#menu-office-select"
    },
    events: {
      "change @ui.officeSelect": "changeOffice"
    },
    changeOffice: function(){
      var officeId = this.ui.officeSelect.val();
      var departments = this.collection.get(officeId).get('departments');
      var departmentsCollection = new StepThrough.Entities.DepartmentCollection(departments);
      var departmentSelectionView = new View.DepartmentSelection({
        menuLayoutView: this.menuLayoutView,
        collection: departmentsCollection
      });
      this.getRegion('departmentSelection').show(departmentSelectionView);
    },
    onShow: function() {
      this.changeOffice();
    }
  });

  View.MenuLayout = Backbone.Marionette.LayoutView.extend({
    template: menuLayoutTpl,
    className: "container-fluid",
    regions: {
      menuSelection: "#menu-selection-container",
      menu: "#nav-menu",
    },
    templateHelpers: function () {
      return {
        baseURL: StepThrough.baseURL
      };
    }
  });
});

export default StepThrough.HeaderApp.Menu.View;