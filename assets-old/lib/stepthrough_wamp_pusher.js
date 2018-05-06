define(["autobahn","app"], function(ab, StepInventory){
	if(!StepInventory.Autobahn){
		StepInventory.Autobahn = {
			onOpenFunctions: []
		};
	}
	if(!StepInventory.Autobahn.connection){
		StepInventory.Autobahn.connection = new ab.Connection({
	      url: 'ws://'+StepInventory.wsAddress,
	      realm: 'step-inventory'
	    });

	    StepInventory.Autobahn.connection.onopen = function(session, details){
	      console.warn('WebSocket connection opened');
	      StepInventory.Autobahn.session = session;

	      for(var i = 0; i< StepInventory.Autobahn.onOpenFunctions.length; i++){
	      	StepInventory.Autobahn.onOpenFunctions[i]();
	      }
	      StepInventory.Autobahn.onOpenFunctions = [];
		};

	    StepInventory.Autobahn.connection.onclose = function(session, details){
	      	console.warn('WebSocket connection closed');
	      	StepInventory.Autobahn.session = null;
	      	StepInventory.Autobahn.connection = new ab.Connection({
		      url: 'ws://'+StepInventory.wsAddress,
		      realm: 'step-inventory'
		    });
	      	StepInventory.Autobahn.connection.open();
	    };
	    StepInventory.Autobahn.connection.open();
	}

	var StepInventoryWampPusher = function(options){
		if(typeof options === 'string'){
			options = {
				topic: options
			};
		}

		if(!options.topic){
			throw "Must supply a topic for subscription";
		}

		var modelList = [];
		this.addModel = function(model){
			modelList.push(model);
		};

		var checkModels = function(args){
			modelUpdate = JSON.parse(args[0]);
			_.each(modelList, function(model){
				if(model.get('id') == modelUpdate.id){
					model.set(modelUpdate);
				}
			});
		};


		if(!StepInventory.Autobahn.session){
		    StepInventory.Autobahn.onOpenFunctions.push(function(){
		    	StepInventory.Autobahn.session.subscribe(options.topic, function(args) {
					checkModels(args);
			    });
		    });
		}else{
			StepInventory.Autobahn.session.subscribe(options.topic, function(args) {
				checkModels(args);
		    });
		}

		return this;
	};


	return StepInventoryWampPusher;
});