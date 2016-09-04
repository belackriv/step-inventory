SystemJS.config({
  baseURL: "/assets",
  production: true,
  paths: {
    "github:": "jspm_packages/github/",
    "npm:": "jspm_packages/npm/",
    "lib/": "lib/"
  }
});
