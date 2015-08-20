define(["lib/app"], function(StepThrough){
  StepThrough.module("Entities", function(Entities, StepThrough, Backbone, Marionette, $, _){
    Entities.Department = Backbone.Model.extend({});

    Entities.DepartmentCollection = Backbone.Collection.extend({
      model: Entities.Department,
      url: StepThrough.baseURL+'/department',
      parse: function(response) {
        return response.list;
      }
    });
  });

  return;
});