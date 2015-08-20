define(["lib/app","lib/apps/admin/admin_layout.hbs!"], function(StepThrough, adminLayoutTpl){

  StepThrough.module("AdminApp.View", function(View, StepThrough, Backbone, Marionette, $, _){

    View.Layout = Backbone.Marionette.LayoutView.extend({
      template: adminLayoutTpl,
      className: 'row',
      regions: {
        sidebar: "#admin-sidebar",
        content: "#admin-content"
      },
      ui: {
        sidebarNav: "#admin-sidebar a"
      },
      events: {
        "click @ui.sidebarNav": "triggerAdminAppRoute"
      },
      triggerAdminAppRoute: function(event){
        event.preventDefault();
        StepThrough.trigger( $(event.target).attr('data-app-trigger') );
      }
    });

	});

  return StepThrough.AdminApp.View;
});