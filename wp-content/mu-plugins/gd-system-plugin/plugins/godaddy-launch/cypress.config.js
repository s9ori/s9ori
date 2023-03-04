const { defineConfig } = require('cypress')

module.exports = defineConfig({
  chromeWebSecurity: false,
  defaultCommandTimeout: 30000,
  fixturesFolder: 'languages',
  screenshotsFolder: '.dev/tests/cypress/screenshots',
  viewportWidth: 2560,
  viewportHeight: 1440,
  env: {
    testURL: 'http://localhost:8889',
    wpUsername: 'admin',
    wpPassword: 'password',
  },
  retries: {
    runMode: 0,
    openMode: 0,
  },
  pageLoadTimeout: 120000,
  e2e: {
    // We've imported your old cypress plugins here.
    // You may want to clean this up later by importing these.
    setupNodeEvents(on, config) {
      return require('./.dev/tests/cypress/plugins/index.js')(on, config)
    },
    supportFile: '.dev/tests/cypress/support/commands.js',
    specPattern: './/**/*.cypress.js',
  },
})
