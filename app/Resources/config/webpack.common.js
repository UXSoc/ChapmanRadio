let webpack = require('webpack')
let ExtractTextPlugin = require('extract-text-webpack-plugin')
let helpers = require('./helper')
let path = require('path')
let CopyWebpackPlugin = require('copy-webpack-plugin')

module.exports = {
  entry: {
    'app': './app/Resources/src/app.js',
    'style': './app/Resources/style/style.scss',
    'vendor': './app/Resources/src/vendor.js'
  },
  resolve: {
    modules: ['node_modules', 'bower_components'],
    descriptionFiles: ['package.json', 'bower.json'],
    extensions: ['.vue', '.js', '.scss', '.ts', '.svg'],
    alias: {
      'parchment': path.resolve(__dirname, '../../../node_modules/parchment/src/parchment.ts'),
      'quill$': path.resolve(__dirname, '../../../node_modules/quill/quill.js')
    }

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
        test: /^((?!quill).)*\.(png|jpe?g|gif|svg|woff|woff2|ttf|eot|ico)$/,
        loader: 'file-loader?name=./[name].[ext]'
      },
      {
        test: /quill.*\.svg$/,
        loader: 'html-loader',
        options: {
          minimize: true
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader!eslint-loader',
        // make sure to exclude 3rd party code in node_modules
        exclude: ['/node_modules/', '/bower_components']
      },
      {
        test: /\.ts$/,
        loader: 'ts-loader',
        options: {
          compilerOptions: {
            declaration: false,
            target: 'es5',
            module: 'commonjs'
          },
          transpileOnly: true
        }
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
    new CopyWebpackPlugin([ {from: 'app/Resources/public', to: 'public'}])
  ]
}
