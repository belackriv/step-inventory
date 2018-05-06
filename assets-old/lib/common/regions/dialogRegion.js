'use strict';

import jquery from 'jquery';
import Marionette from 'marionette';
import 'jquery-ui/themes/base/jquery-ui.css!';

export default Marionette.Region.extend({
	el: '#dialog-content',
	getEl(el){
		return jquery(el);
	}
});