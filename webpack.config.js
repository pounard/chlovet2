var Encore = require('@symfony/webpack-encore');

// @see https://symfony.com/doc/current/frontend.html
Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    //.setManifestKeyPrefix('build/') // For CDN (useless now)

    .addEntry('app.client', './assets/js/app.client.ts')
    .addEntry('app.gestion', './assets/js/app.gestion.ts')

    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .enableSassLoader()
    .enableTypeScriptLoader()

    .autoProvidejQuery() // For Bootstrap
    .enableReactPreset()
;

module.exports = Encore.getWebpackConfig();
