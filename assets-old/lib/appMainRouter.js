'use strict';

import Marionette from 'marionette';
import CommonRouter from './common/router.js';
import AdminRouter from './admin/router.js';
import InventoryRouter from './inventory/router.js';
import ReportingRouter from './reporting/router.js';


export default Marionette.AppRouter.extend({
  initialize(){
  	new CommonRouter({appRouter:this});
    new AdminRouter({appRouter:this});
    new InventoryRouter({appRouter:this});
    new ReportingRouter({appRouter:this});
  }
});