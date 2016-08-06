SystemJS.config({
  nodeConfig: {
    "paths": {
      "lib/": "lib/"
    }
  },
  transpiler: "plugin-babel",
  packages: {
    "lib": {
      "format": "esm",
      "main": "main.js",
      "meta": {
        "*.js": {
          "loader": "plugin-babel"
        }
      }
    }
  }
});

SystemJS.config({
  packageConfigPaths: [
    "npm:@*/*.json",
    "npm:*.json",
    "github:*/*.json"
  ],
  map: {
    "backbone": "npm:backbone@1.3.3",
    "backbone.babysitter": "github:marionettejs/backbone.babysitter@0.1.11",
    "backbone.paginator": "npm:backbone.paginator@2.0.5",
    "backbone.radio": "github:marionettejs/backbone.radio@1.0.4",
    "backbone.relational": "github:VitalStorm/Backbone-relational@master",
    "backbone.stickit": "github:VitalStorm/backbone.stickit@master",
    "backbone.syphon": "github:marionettejs/backbone.syphon@0.6.3",
    "backbone.wreqr": "github:marionettejs/backbone.wreqr@1.3.6",
    "bulma": "npm:bulma@0.1.2",
    "css": "github:systemjs/plugin-css@0.1.21",
    "handlebars": "github:components/handlebars.js@4.0.5",
    "hbs": "github:belackriv/plugin-hbs@jspm-.17",
    "jquery": "npm:jquery@3.1.0",
    "jquery-datetimepicker": "github:xdan/datetimepicker@2.4.5",
    "jquery-ui": "github:components/jqueryui@1.12.0",
    "marionette": "github:marionettejs/backbone.marionette@3.0.0-pre.4",
    "moment": "npm:moment@2.13.0",
    "moment-duration-format": "npm:moment-duration-format@1.3.0",
    "moment-timezone": "npm:moment-timezone@0.5.4",
    "plugin-babel": "npm:systemjs-plugin-babel@0.0.13",
    "process": "github:jspm/nodelibs-process@0.2.0-alpha",
    "select2": "github:select2/select2@4.0.3",
    "underscore": "npm:underscore@1.8.3"
  },
  packages: {
    "github:select2/select2@4.0.3": {
      "map": {
        "jquery": "npm:jquery@3.1.0"
      }
    },
    "github:components/jqueryui@1.12.0": {
      "map": {
        "jquery": "npm:jquery@3.1.0"
      }
    },
    "npm:backbone.paginator@2.0.5": {
      "map": {
        "backbone": "npm:backbone@1.3.3",
        "underscore": "npm:underscore@1.8.3"
      }
    },
    "npm:backbone@1.3.3": {
      "map": {
        "underscore": "npm:underscore@1.8.3"
      }
    },
    "npm:moment-timezone@0.5.4": {
      "map": {
        "moment": "npm:moment@2.13.0"
      }
    }
  }
});
