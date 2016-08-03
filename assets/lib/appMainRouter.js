'use strict';

import Marionette from 'marionette';
import AdminRouter from './admin/router.js';
import InventoryRouter from './inventory/router.js';


export default Marionette.AppRouter.extend({
  initialize(){
    new AdminRouter({appRouter:this});
    new InventoryRouter({appRouter:this});
  }
});