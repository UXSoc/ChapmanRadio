const webpack = require('webpack')
const webpackMerge = require('webpack-merge')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const commonConfig = require('./webpack.common.js')
const helpers = require('./helper')
const path = require('path')

const ENV = process.env.NODE_ENV = process.env.ENV = 'hotload'

module.exports = webpackMerge(commonConfig, {
  devtool: 'source-map',
  devServer: {
    contentBase: helpers.root('web/'),
    host: '0.0.0.0',
    hot: true,
    overlay: true,
    port: 9000,
    stats: {
      colors: true
    },
    headers: {
      'Access-Control-Allow-Origin': 'http://localhost:8000',
      'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
      'Access-Control-Allow-Headers': 'X-Requested-With, content-type, Authorization',
      'Access-Control-Allow-Credentials': 'true'
    }
  },
  output: {
    publicPath: 'http://localhost:9000/bundles/'
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'ENV': JSON.stringify(ENV)
      }
    }),
    new webpack.HotModuleReplacementPlugin()
  ]
})
