SystemJS.config({
  //baseURL: "/~belac/stepthrough/assets",
  baseURL: "/assets",
  production: true,
  paths: {
    "github:": "jspm_packages/github/",
    "npm:": "jspm_packages/npm/",
    "lib/": "lib/"
  }
});
