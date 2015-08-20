define(["lib/app","lib/apps/admin/users/list_view.hbs!", "lib/apps/admin/users/list_item.hbs!"], function(StepThrough, userListViewTpl, userListItemTpl){

  StepThrough.module("AdminApp.Users.View", function(View, StepThrough, Backbone, Marionette, $, _){

    View.ListItem = Backbone.Marionette.CompositeView.extend({
      behaviors: {
        OutlineAlertOnChange:{}
      },
      template: userListItemTpl,
      tagName: 'tr',
      modelEvents: {
        "change": 'render'
      },
      ui: {
        userActiveCheckbox: ".user-active-checkbox"
      },
      events: {
        "click @ui.userActiveCheckbox": "setUserIsActive",

      },
      setUserIsActive: function(event){
        var isActive = $(event.target).prop('checked');
        this.model.save({isActive:isActive},{patch:true});
      }
    });

    View.ListView = Backbone.Marionette.CompositeView.extend({
      template: userListViewTpl,
      childView: View.ListItem,
      childViewContainer: "tbody",
      ui: {
        usernameSearchButton: "#username-search-button"
      },
      events: {
        "click @ui.usernameSearchButton": "usernameSearch",

      },
      usernameSearch: function(event){
        var username = this.$el.find('#user_search_input').val();
        StepThrough.trigger('admin:users:list', {username:username} );
      }
    });

  });

  return StepThrough.AdminApp.Users.View;
});