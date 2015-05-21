# CrudKit

[![GitHub license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://github.com/skyronic/crudkit/blob/master/LICENSE)

-----------

A Toolkit to quickly build powerful mobile-friendly CRUD (create/read/update/delete) interfaces for PHP, Laravel, and Codeigniter apps. http://crudkit.com

## Build locally
Additionally from having PHP installed, make sure you have [nodejs](https://nodejs.org/) installed.

Once you have the basic requirements, follow the below instructions:

1. Clone the repository `$ git clone git@github.com:skyronic/crudkit.git`

2. Install [composer](https://getcomposer.org).

3. Install / Update dependencies, `$ composer update`.

4. Install `grunt` and other node related packages using: `$ npm install` (You might need to use `sudo`)

5. Install client-side dependencies using: `$ bower install`

## Demos
Demos are listed in `demo/`. Inorder to see the demos in action follow the steps listed below:

- Create a symlink of `crudkit` using: `$ ln -s /home/<username>/path/to/crudkit demo/crudkit`
- Build static files: `$ grunt buildStatic`
- Start the server by running `$ php -S 0.0.0.0:8080` from the root of the project
- Navigate to http://localhost:8080/demo/sql_basic.php
