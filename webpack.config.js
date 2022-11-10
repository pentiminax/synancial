const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    /*
     * ENTRY CONFIG
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('checking_list', './assets/js/wallet/checking/list.js')
    .addEntry('checking_view', './assets/js/wallet/checking/view.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('documents_list', './assets/js/documents/list.js')
    .addEntry('loans_list', './assets/js/wallet/loans/list.js')
    .addEntry('market_list', './assets/js/wallet/market/list.js')
    .addEntry('market_view', './assets/js/wallet/market/view.js')
    .addEntry('savings_list', './assets/js/wallet/savings/list.js')
    .addEntry('settings', './assets/js/settings.js')
    .addEntry('wallet', './assets/js/wallet.js')
    .addEntry('wallet_list', './assets/js/wallet_list.js')

    .splitEntryChunks()

    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
