Laravel 4 - Simple Modules
=========================

[![Build Status](https://travis-ci.org/pingpong-labs/modules.svg?branch=master)](https://travis-ci.org/pingpong-labs/modules)
[![Latest Stable Version](https://poser.pugx.org/pingpong/modules/v/stable.svg)](https://packagist.org/packages/pingpong/modules)
[![Total Downloads](https://poser.pugx.org/pingpong/modules/downloads.svg)](https://packagist.org/packages/pingpong/modules)
[![Latest Unstable Version](https://poser.pugx.org/pingpong/modules/v/unstable.svg)](https://packagist.org/packages/pingpong/modules)
[![License](https://poser.pugx.org/pingpong/modules/license.svg)](https://packagist.org/packages/pingpong/modules)

### Server Requirements

- PHP 5.4 or higher

### Donation

If you find this source useful, you can share some milk to me if you want ^_^

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=95ZPLBB8U3T9N)

### Changes Log

**1.0.10 to 1.0.11**

- See [#26](https://github.com/pingpong-labs/modules/pull/26)

**1.0.9 to 1.0.10**

- Added support for [#13](https://github.com/pingpong-labs/modules/pull/13) : enable and disable module.
- There is new artisan command `module:enable` and `module:disable`.

**1.0.8 to 1.0.9**

- Fix `module:seed-make` command when running `module:make` command.
- Command Improvement.

**1.0.7 to 1.0.8**

- There is command name changed :
  -  `php artisan module:migrate:make` to `php artisan module:migration`
  -  `php artisan module:seed:make` to `php artisan module:seed-make`
- Merged [#8](https://github.com/pingpong-labs/modules/pull/18) : Fix constructor error.
- Package improvement.

### Installation

Open your composer.json file and add a new required package.
  ```
  "pingpong/modules": "1.0.*" 
  ```
Next, open a terminal and run.
  ```
  composer update 
  ```
Next, Add new service provider in `app/config/app.php`.
  ```php
  'Pingpong\Modules\ModulesServiceProvider',
  ```
Next, Add new class alias in `app/config/php`.
  ```php
  'Module'        => 'Pingpong\Modules\Facades\Module',
  ```
Next, publish package configuration. Open your terminal and run:
  ```
  php artisan config:publish pingpong/modules
  ```
Done.

### Setup modules folder for first use

By default modules folder is in your Laravel route directory. For first use, please run this command on your terminal.
  ```
  php artisan module:setup
  ```

### Folder Structure
  
  Now, naming modules must use a capital letter on the first letter. For example: Blog, News, Shop, etc.

  ```
  app/
  public/
  vendor/
  Modules
  |-- Blog
      |-- commands/
      |-- config/
      |-- controllers/
      |-- database/
          |-- migrations/
          |-- seeds/
      |-- models/
      |-- start/
          |-- global.php
      |-- tests/
      |-- views/
      |-- BlogServiceProvider.php
      |-- filters.php
      |-- routes.php
      |-- module.json
  ```

  **Note:** File `start/global.php` is required for registering `View`, `Lang` and `Config` namespaces. If that file does not exist, an exception `FileMissingException` is thrown.

### Artisan CLI
  
Create new module.

  ```
  php artisan module:make blog
  ```
  
Create new command for the specified module.
  
  ```
  php artisan module:command CustomCommand blog

  php artisan module:command CustomCommand blog --command=custom:command

  php artisan module:command CustomCommand blog --namespace=Modules\Blog\Commands
  ```
  
Create new migration for the specified module.

  ```
  php artisan module:migration users blog

  php artisan module:migration users blog --fields="username:string, password:string"
  ```
  
Create new seed for the specified module.

  ```
  php artisan module:seed-make users blog
  ```
  
Migrate from the specified module.

  ```
  php artisan module:migrate blog
  ```
  
Migrate from all modules.

  ```
  php artisan module:migrate
  ```
  
Seed from the specified module.

  ```
  php artisan module:seed blog
  ```
  
Seed from all modules.
 
  ```
  php artisan module:seed
  ```

Create new controller for the specified module.

  ```
  php artisan module:controller SiteController blog
  ```

Publish assets from the specified module to public directory.

  ```
  php artisan module:publish blog
  ```

Publish assets from all modules to public directory.

  ```
  php artisan module:publish
  ```

Create new model for the specified module.

  ```
  php artisan module:model User blog
  ```

Publish migration for the specified module or for all modules.
    This helpful when you want to rollback the migrations. You can also run `php artisan migrate` instead of `php artisan module:migrate` command for migrate the migrations.

For the specified module.
```
php artisan module:publish-migration blog
```

For all modules.
```
php artisan module:publish-migration
```

Enable the specified module.

```
    php artisan module:enable blog
```

Disable the specified module.

```
    php artisan module:disable blog
```

### Facades API

Get asset url from specified module.

  ```php
  Module::asset('blog', 'image/news.png');
  ```

Generate new stylesheet tag.

  ```php
  Module::style('blog', 'image/all.css');
  ```

Generate new stylesheet tag.

  ```php
  Module::script('blog', 'js/all.js');
  ```

Get all modules.

  ```php
  Module::all();
  ```

Get all enabled module.
```php
    Module::enabled();
```

Get all disabled module.
```php
    Module::disabled();
```

Get modules path.

  ```php
  Module::getPath();
  ```

Get assets modules path.

  ```php
  Module::getAssetsPath();
  ```

Get module path for the specified module.

  ```php
  Module::getModulePath('blog');
  ```

Enable a specified module.
```php
    Module::enable('blog')
```

Disable a specified module.
```php
    Module::disable('blog')
```

Get module json property as array from a specified module.
```php
    Module::getProperties('blog')
```

**NEW!**

Use the specified module.

```php
php artisan module:use blog
``` 

### Custom Service Provider

  When you create your create new module, it also creates a new custom service provider. For example, if you create a new module named `blog`, it also creates a new Service Provider named `BlogServiceProvider` with namespace `Modules\Blog`. I think it is useful for registering custom command for each module. This file is not autoloaded; you can autoload this file using `psr-0` or `psr-4`. That file maybe look like this:

  ```php
  <?php namespace Modules\Blog;

  use Illuminate\Support\ServiceProvider;

  class BlogServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
      //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
      return array();
    }

  }

  ```

### Custom Namespaces

  When you create a new module it also registers new custom namespace for `Lang`, `View` and `Config`. For example, if you create a new module named `blog`, it will also register new namespace/hint `blog` for that module. Then, you can use that namespace for calling `Lang`, `View` or `Config`.
  Following are some examples of its usage:

  Calling Lang:
  ```php
  Lang::get('blog::group.name')
  ```

  Calling View:
  ```php
  View::make('blog::index')

  View::make('blog::partials.sidebar')
  ```

  Calling Config:
  ```php
  Config::get('blog::group.name')
  ```

### License

  This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
