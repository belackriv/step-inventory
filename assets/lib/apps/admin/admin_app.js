define(["lib/app"], function(StepThrough){
  StepThrough.module("AdminApp", function(AdminApp, StepThrough, Backbone, Marionette, $, _){
    AdminApp.startWithParent = false;

    AdminApp.on("start", function(){
      console.log('admin app started');
      require(["lib/apps/admin/admin_view"], function(adminView){
        var adminLayoutView = new adminView.Layout();
        StepThrough.appLayout.getRegion('main').show(adminLayoutView);
        AdminApp.appLayout = adminLayoutView;
        AdminApp.trigger("layout:ready");
      });
    });

    AdminApp.on("stop", function(){
      console.log('admin app stopped');
    });

  });

  StepThrough.module("Routers.AdminApp", function(AdminAppRouter, StepThrough, Backbone, Marionette, $, _){
    AdminAppRouter.Router = Marionette.AppRouter.extend({
      appRoutes: {
        //"contacts(/filter/criterion::criterion)": "listContacts",
        "admin(/)": "showAdmin",
        "admin/users(/)": "listUsers",
        "admin/menu_links(/)": "listMenuLinks",
        
      }
    });

    var executeAction = function(action, arg){
      StepThrough.startSubApp("AdminApp");
      action(arg);
    };

    var activateNavLinkItem = function(href){
      var doActivate = function(){
        var activeNavLinkItem = StepThrough.AdminApp.appLayout.$el.find('[href="'+href+'"]').parent();
        activeNavLinkItem.addClass('active');
        activeNavLinkItem.siblings().removeClass('active');
        StepThrough.AdminApp.activeRoute = href;
      };
      if(StepThrough.AdminApp.activeRoute != href){
        if(StepThrough.AdminApp.appLayout){
          doActivate();
        }else{
          StepThrough.AdminApp.once("layout:ready", doActivate);
        }
        return true;
      }else{
        return false;
      }
    };

    var API = {
      showAdmin: function(){
         StepThrough.startSubApp("AdminApp");
      },
      listUsers: function(criteria){
        if(activateNavLinkItem('admin/users') || StepThrough.AdminApp.Users.lastSearchedCriteria != criteria){
          require(["lib/apps/admin/users/list_controller"], function(ListController){
            executeAction(ListController.listUsers, criteria);
          });
        }
      },
      listMenuLinks: function(criteria){
        if(activateNavLinkItem('admin/menu_links')){
          require(["lib/apps/admin/users/list_controller"], function(ListController){
            executeAction(ListController.listUsers, criteria);
          });
        }
      }
/*      
      listContacts: function(criterion){
        require(["apps/contacts/list/list_controller"], function(ListController){
          executeAction(ListController.listContacts, criterion);
        });
      },

      showContact: function(id){
        require(["apps/contacts/show/show_controller"], function(ShowController){
          executeAction(ShowController.showContact, id);
        });
      },

      editContact: function(id){
        require(["apps/contacts/edit/edit_controller"], function(EditController){
          executeAction(EditController.editContact, id);
        });
      }
*/      
    };

    StepThrough.on("admin:show", function(){
      StepThrough.navigate("admin");
      API.showAdmin();
    });

    StepThrough.on("admin:users:list", function(criteria){
      StepThrough.navigate("admin/users");
      API.listUsers(criteria);
    });

    StepThrough.on("admin:menu_links:list", function(){
      StepThrough.navigate("admin/menu_links");
      API.listMenuLinks();
    });
/*
     StepThrough.on("contacts:list", function(){
      StepThrough.navigate("contacts");
      API.listContacts();
    });

    StepThrough.on("contacts:filter", function(criterion){
      if(criterion){
        StepThrough.navigate("contacts/filter/criterion:" + criterion);
      }
      else{
        StepThrough.navigate("contacts");
      }
    });

    StepThrough.on("contact:show", function(id){
      StepThrough.navigate("contacts/" + id);
      API.showContact(id);
    });

    StepThrough.on("contact:edit", function(id){
      StepThrough.navigate("contacts/" + id + "/edit");
      API.editContact(id);
    });
*/
    StepThrough.addInitializer(function(){
      new AdminAppRouter.Router({
        controller: API
      });
    });
  });

  return StepThrough.AdminApp;
});