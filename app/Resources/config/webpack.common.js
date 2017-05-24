var webpack = require('webpack')
var ExtractTextPlugin = require('extract-text-webpack-plugin')
var helpers = require('./helper')
var path = require('path')
var CopyWebpackPlugin = require('copy-webpack-plugin')

module.exports = {
  entry: {
    'app': './app/Resources/src/app.js',
    'style': './app/Resources/style/style.scss',
    'vendor': './app/Resources/src/vendor.js'
  },
  resolve: {
    modules: ['node_modules', 'bower_components'],
    descriptionFiles: ['package.json', 'bower.json'],
    extensions: ['.vue', '.js', '.scss']
  },

  output: {
    path: helpers.root('web/bundles/'),
    publicPath: '/bundles/',
    filename: '[name].js',
    chunkFilename: '[id].[hash].chunk.js'
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
        test: /\.js$/,
        loader: 'babel-loader!eslint-loader',
        // make sure to exclude 3rd party code in node_modules
        exclude: ['/node_modules/', '/bower_components']
      },
      {
        test: /\.css/,
        loader: ['style-loader', 'css-loader']
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
    new ExtractTextPlugin('./[name].css'),
    new webpack.optimize.CommonsChunkPlugin({
      name: ['vendor']
    }),
    new CopyWebpackPlugin([ {from : 'bower_components/tinymce/skins', to: 'skins'}])
  ]
}