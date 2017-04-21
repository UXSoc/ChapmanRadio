var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var helpers = require('./helper');
var path = require('path');

module.exports = {
    entry: {
        'app': './app/Resources/src/app.js',
        'style': './app/Resources/style/style.scss'
    },
    resolve: {
        modules: ["node_modules","bower_components"],
        extensions: ['.vue', '.js','.scss']
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                loaders: ExtractTextPlugin.extract({ fallbackLoader: 'style-loader', loader: 'css-loader!resolve-url-loader!sass-loader?sourceMap=true' })
            },
            {
                test: /\.(png|jpe?g|gif|svg|woff|woff2|ttf|eot|ico)$/,
                loader: 'file-loader?name=./[name].[ext]'
            },
            {
                test: /\.css/,
                loader: ["style-loader", "css-loader"]
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        js: 'babel-loader!eslint-loader'
                    }
                }
            }
        ]
    },

    plugins: [
        new ExtractTextPlugin('./css/[name].css'),
        new webpack.optimize.CommonsChunkPlugin({
            name: ['app', 'vendor', 'polyfills']
        })
    ]
};