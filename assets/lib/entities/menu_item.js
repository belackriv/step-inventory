define(["lib/app"], function(StepThrough){
  StepThrough.module("Entities", function(Entities, StepThrough, Backbone, Marionette, $, _){
    Entities.MenuItem = Backbone.Model.extend({});

    Entities.MenuItemCollection = Backbone.Collection.extend({
      model: Entities.MenuItem,
      url: StepThrough.baseURL+'/menu_item',
      parse: function(response) {
        return response.list;
      }
    });
  });

  return ;
});