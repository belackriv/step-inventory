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
        },
        "*.hbs": {
          "loader": "hbs"
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
    "assert": "npm:jspm-nodelibs-assert@0.2.0",
    "backbone": "npm:backbone@1.3.3",
    "backbone.babysitter": "github:marionettejs/backbone.babysitter@0.1.12",
    "backbone.paginator": "npm:backbone.paginator@2.0.5",
    "backbone.radio": "github:marionettejs/backbone.radio@1.0.5",
    "backbone.relational": "github:VitalStorm/Backbone-relational@master",
    "backbone.stickit": "github:VitalStorm/backbone.stickit@master",
    "backbone.syphon": "github:marionettejs/backbone.syphon@0.6.3",
    "backbone.wreqr": "github:marionettejs/backbone.wreqr@1.4.0",
    "child_process": "npm:jspm-nodelibs-child_process@0.2.0",
    "css": "github:systemjs/plugin-css@0.1.32",
    "fs": "npm:jspm-nodelibs-fs@0.2.0",
    "handlebars": "github:components/handlebars.js@4.0.5",
    "hbs": "github:davis/plugin-hbs@1.2.3",
    "jquery": "npm:jquery@3.1.1",
    "jquery-datetimepicker": "github:xdan/datetimepicker@2.4.5",
    "jquery-ui": "github:components/jqueryui@1.12.1",
    "jsbarcode": "npm:jsbarcode@3.5.7",
    "marionette": "github:marionettejs/backbone.marionette@3.1.0",
    "moment": "npm:moment@2.17.1",
    "moment-duration-format": "npm:moment-duration-format@1.3.0",
    "moment-timezone": "npm:moment-timezone@0.5.10",
    "papa": "npm:papaparse@4.3.3",
    "path": "npm:jspm-nodelibs-path@0.2.3",
    "plugin-babel": "npm:systemjs-plugin-babel@0.0.13",
    "process": "npm:jspm-nodelibs-process@0.2.0",
    "select2": "github:select2/select2@4.0.3",
    "text": "github:systemjs/plugin-text@0.0.9",
    "underscore": "npm:underscore@1.8.3"
  },
  packages: {
    "github:select2/select2@4.0.3": {
      "map": {
        "jquery": "npm:jquery@3.1.1"
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
    "github:davis/plugin-hbs@1.2.3": {
      "map": {
        "handlebars": "github:components/handlebars.js@4.0.5"
      }
    },
    "npm:moment-timezone@0.5.10": {
      "map": {
        "moment": "npm:moment@2.17.1"
      }
    },
    "github:components/jqueryui@1.12.1": {
      "map": {
        "jquery": "npm:jquery@3.1.1"
      }
    },
    "github:VitalStorm/backbone.stickit@master": {
      "map": {
        "backbone": "npm:backbone@1.3.3"
      }
    },
    "npm:jsbarcode@3.5.7": {
      "map": {
        "jquery": "npm:jquery@3.1.1"
      }
    }
  }
});
