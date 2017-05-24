Chapman Radio
========

A Symfony project created on April 16, 2017, 6:07 pm.

### Libraries

- Bootstarp: http://getbootstrap.com/
- Vue: https://vuejs.org/
- Webpack: https://webpack.js.org/
- Symfony: https://symfony.com/
- VeeValidation: http://vee-validate.logaretm.com/

### Requirments

- PHP
- Mysql or MariaDB

You don't need apache to run this for development since php itself has it's own internal server

### Installing

This project requires Composer, Yarn or npm and bower. Composer is used to install all the php modules for Symfony. npm and yarn will install all the required modules for building the static javascript, html and css. bower is used to install all the minor libraries such as Jquery, and Bootstrap.

- Bower: https://bower.io/
- Yarn: https://yarnpkg.com/en/ Or npm: https://www.npmjs.com/
- composer: https://getcomposer.org/

```
composer install
yarn install
bower install
```

During the composer installation, Composer should ask you for the configurations to connect the site to the database, name, keys etc..

### Getting Started

## Building The Static Stuff

package.json has been configured with some helper to simplify the build process for the static porition of the app.

- 'yarn run build' or 'npm run build'

Build the static html, css and javascript and output the results into web/bundle

- 'yarn run watch' or 'npm run watch'

Build the static html, css and javascript and output the results into web/bundle. Watches directory for changes and only updates those sections that are changed. Allows the developer to make changes and test. 

- 'yarn run production' or 'npm run production'

Runs a couple secondary build procedures to compress and optimize the site. 

- 'yarn run lint' or 'npm run lint'

Runs a linter on the code for consistency

```
"build": "webpack --config app/Resources/config/webpack.dev.js  --progress --profile --display-error-details",
"watch": "webpack --config app/Resources/config/webpack.dev.js  --progress --profile --display-error-details --watch",
"production": "webpack --config app/Resources/config/webpack.prod.js  --progress --profile --display-error-details",
"lint": "eslint --ext .js,.vue src"
```

## The Server

Symfony has a file under /bin/console used for running commands for the application( Migrating the Database, verifying database model, clearing cache, etc.. ). You can run ./bin/console to see all the possible commands but the three below are the bare minimum to getting the application started.

The first step after cloning is to run the migration and setup the database.

```
./bin/console doctrine:migrations:migrate
```

As a developer you probably want something in the database to test on.

```
./bin/console doctrine:fixtures:load 
```

To actually run the server and see if the app works.

```
./bin/console server:run
```


Whew!!




