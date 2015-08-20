import StepThrough from 'lib/app';
import View from './menu_view';
import 'lib/entities/office';
import 'lib/entities/department';
import 'lib/entities/menu_item';

StepThrough.module("HeaderApp.Menu", function(Menu, StepThrough, Backbone, Marionette, $, _){
  var  officeChannel = Backbone.Radio.channel('office');
  Menu.Controller = {
    displayHeaderMenu: function(){
      officeChannel.on('entities:offices:initialized', function(offices){
        var menuLayoutView = new View.MenuLayout();
        StepThrough.appLayout.getRegion('navbar').show(menuLayoutView);

        var menuSelectionLayoutView = new View.MenuSelection({
          menuLayoutView: menuLayoutView,
          collection: offices
        });
        menuLayoutView.getRegion('menuSelection').show(menuSelectionLayoutView);
      });
      officeChannel.command("entities:offices:initialize");
    }
  };
});

export default StepThrough.HeaderApp.Menu.Controller;
