define(["autobahn","app"], function(ab, StepThrough){
	if(!StepThrough.Autobahn){
		StepThrough.Autobahn = {
			onOpenFunctions: []
		};
	}
	if(!StepThrough.Autobahn.connection){
		StepThrough.Autobahn.connection = new ab.Connection({
	      url: 'ws://'+StepThrough.wsAddress,
	      realm: 'stepthrough'
	    });

	    StepThrough.Autobahn.connection.onopen = function(session, details){
	      console.warn('WebSocket connection opened');
	      StepThrough.Autobahn.session = session;

	      for(var i = 0; i< StepThrough.Autobahn.onOpenFunctions.length; i++){
	      	StepThrough.Autobahn.onOpenFunctions[i]();
	      }
	      StepThrough.Autobahn.onOpenFunctions = [];
		};

	    StepThrough.Autobahn.connection.onclose = function(session, details){
	      	console.warn('WebSocket connection closed');
	      	StepThrough.Autobahn.session = null;
	      	StepThrough.Autobahn.connection = new ab.Connection({
		      url: 'ws://'+StepThrough.wsAddress,
		      realm: 'stepthrough'
		    });
	      	StepThrough.Autobahn.connection.open();
	    };
	    StepThrough.Autobahn.connection.open();
	}

	var StepThroughWampPusher = function(options){
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


		if(!StepThrough.Autobahn.session){
		    StepThrough.Autobahn.onOpenFunctions.push(function(){
		    	StepThrough.Autobahn.session.subscribe(options.topic, function(args) {
					checkModels(args);
			    });
		    });
		}else{
			StepThrough.Autobahn.session.subscribe(options.topic, function(args) {
				checkModels(args);
		    });
		}

		return this;
	};


	return StepThroughWampPusher;
});