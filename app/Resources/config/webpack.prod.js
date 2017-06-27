var webpack = require('webpack')
var webpackMerge = require('webpack-merge')
var ExtractTextPlugin = require('extract-text-webpack-plugin')
var commonConfig = require('./webpack.common.js')
var helpers = require('./helper')
var path = require('path')

const ENV = process.env.NODE_ENV = process.env.ENV = 'production'

module.exports = webpackMerge(commonConfig, {
  devtool: 'source-map',

  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'ENV': JSON.stringify(ENV)
      }
    }),
    new webpack.optimize.UglifyJsPlugin(),
    new webpack.optimize.AggressiveMergingPlugin()
  ]
})
