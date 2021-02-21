module.exports = {
    devServer: {
        disableHostCheck: true,
        port: 8080,
        public: '0.0.0.0:8080',
        lintOnSave: false,
        clientLogLevel: 'info'
    },
    publicPath: "/",
    configureWebpack: {
        output: {
            publicPath: '/'
        }
    },
    productionSourceMap: false,
    css: { extract: false },
    chainWebpack: config => {
        config.module.rule('svg')
            .use('file-loader')
            .loader('url-loader')
            .tap(options => {
                options.limit = 300000
                return options
            })
    }
}