const webpack = require('webpack')
const webpackMerge = require('webpack-merge')
// let ExtractTextPlugin = require('extract-text-webpack-plugin')
const commonConfig = require('./webpack.common.js')
// let helpers = require('./helper')
// let path = require('path')

const ENV = process.env.NODE_ENV = process.env.ENV = 'dev'

module.exports = webpackMerge(commonConfig, {
  devtool: 'source-map',
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'ENV': JSON.stringify(ENV)
      }
    })
  ]
})
