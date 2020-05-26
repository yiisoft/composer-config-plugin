<p align="center">    
    <img src="logo.png" height="126px">
    <h1 align="center">Composer Config Plugin</h1>
    <br>
</p>

Composer plugin for config assembling.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/composer-config-plugin/v/stable)](https://packagist.org/packages/yiisoft/composer-config-plugin)
[![Total Downloads](https://poser.pugx.org/yiisoft/composer-config-plugin/downloads)](https://packagist.org/packages/yiisoft/composer-config-plugin)
[![Build status](https://github.com/yiisoft/composer-config-plugin/workflows/build/badge.svg)](https://github.com/yiisoft/composer-config-plugin/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/composer-config-plugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/composer-config-plugin/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/composer-config-plugin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/composer-config-plugin/?branch=master)

This [Composer] plugin provides assembling
of configurations distributed with composer packages.
It allows putting configuration needed to use a package right inside of
the package thus implementing a plugin system. The package becomes a plugin
holding both the code and its configuration.

How it works?

- Scans installed packages for `config-plugin` extra option in their
  `composer.json`.
- Loads `.env` files to set `$_ENV` variables.
- Requires `constants` files to set constants.
- Requires `params` files.
- Requires config files.
- Options collected during earlier steps could and should be used in later
  steps, e.g. `$_ENV` should be used for constants and parameters, which
  in turn should be used for configs.
- File processing order is crucial to achieve expected behavior: options
  in root package have priority over options from included packages. It is described
  below in **File processing order** section.
- Collected configs are written as PHP files in
  `vendor/yiisoft/composer-config-plugin-output`
  directory along with information needed to rebuild configs on demand.
- Then assembled configs are ready to be loaded into application using `require`.

**Read more** about the general idea behind this plugin in [English] or
[Russian].

[Composer]: https://getcomposer.org/
[English]:  https://hiqdev.com/pages/articles/app-organization
[Russian]:  https://habrahabr.ru/post/329286/

## Installation

```sh
composer require "yiisoft/composer-config-plugin"
```

Out of the box this plugin supports configs in PHP and JSON formats.

To enable additional formats require:

- [vlucas/phpdotenv] - for `.env` files.
- [symfony/yaml] - for YAML files, `.yml` and `.yaml`.

[vlucas/phpdotenv]: https://github.com/vlucas/phpdotenv
[symfony/yaml]: https://github.com/symfony/yaml

## Usage

List your config files in `composer.json` like the following:

```json
"extra": {
    "config-plugin-output-dir": "path/relative-to-composer-json",
    "config-plugin": {
        "envs": "db.env",
        "params": [
            "config/params.php",
            "?config/params-local.php"
        ],
        "common": "config/common.php",
        "web": [
            "$common",
            "config/web.php"
        ],
        "other": "config/other.php"
    }
},
```

`?` marks optional files. Absence of files not marked with it will cause exception.

`$common` is inclusion - `common` config will be merged into `web`.

Define your configs like the following:

```php
return [
    'components' => [
        'db' => [
            'class' => \my\Db::class,
            'name' => $params['db.name'],
            'password' => $params['db.password'],
        ],
    ],
];
```

To load assembled configs in your application use `require`:

```php
$config = require Yiisoft\Composer\Config\Builder::path('web');
```

### Refreshing config

Plugin uses composer `POST_AUTOLOAD_DUMP` event i.e. composer runs this plugin on `install`, `update` and `dump-autoload`
commands. As the result configs are ready to be used right after package installation or update.

When you make changes to any of configs you may want to reassemble configs manually. In order to do it run:

```sh
composer dump-autoload
```

Above can be shortened to `composer du`.

If you need to force config rebuilding from your application, you can do it like the following:

```php
// Don't do it in production, assembling takes it's time
if (ENVIRONMENT === 'dev') {
    Yiisoft\Composer\Config\Builder::rebuild();
}
```

### File processing order

Config files are processed in proper order to achieve naturally expected
behavior:

- Options in outer packages override options from inner packages.
- Plugin respects the order your configs are listed in `composer.json` with.
- Different types of options are processed in the following order:
    - Environment variables from `envs`.
    - Constants from `constants`.
    - Parameters from `params`.
    - Configs are processed last of all.

### Debugging

There are several ways to debug the config building internals.

- Plugin can show detected package dependencies hierarchy by running:

```sh
composer dump-autoload --verbose
```

Above can be shortened to `composer du -v`.

- You can see the list of configs and files that plugin has detected and uses
to build configs. It is located in `vendor/yiisoft/composer-config-plugin-output/__files.php`.

- You can see the assembled configs in the output directory which is
`vendor/yiisoft/composer-config-plugin-output` by default and can be configured
with `config-plugin-output-dir` extra option in `composer.json`.


## Using environment variables

Environment variables could be used in two ways:

- In runtime.
- In build time.

### Runtime

Passing environment variables in runtime is preferred way since variables could be specific to the server.
That is a good way to store and apply sensitive data. Recommended solution
is [HashiCorp Vault](https://www.vaultproject.io/).

Runtime environment variables could be accessed by using a special wrapper in your `config/params.php`:

```php
'apiEndpoint' => \Yiisoft\Composer\Config\Env::get('apiEndpoint', 'https://yiipowered.com/en/api/1.0'),
```

After the config is built, you'll get

```php
'apiEndpoint' => getenv('apiEndpoint') ?? 'https://yiipowered.com/en/api/1.0',
``` 

### Build time

Environmental variables at build time are not as useful as at runtime. Still, there are cases when they could come
in handy.

It could be done by using `getenv()` in your `config/params.php`:

```php
'apiEndpoint' => getenv('apiEndpoint') ?? 'https://yiipowered.com/en/api/1.0',
```

After the config is built, you'll get

```php
'apiEndpoint' => 'https://yiipowered.com/en/api/1.0',
```

Or whatever ends up in `apiEndpoint` environment variable at config build time. For example,

```
apiEndpoint="https://example.com" composer du
```

will give you

```php
'apiEndpoint' => 'https://example.com',
```

There is a convention that any parameter, even it `dotenv()` isn't used explicitly, could be overridden with a specially
named variable. For example, we have the following `params.php`.

```php
return [
    'my-service.option-name' => null,
    'db' => [
        'pgsql' => [
            'password' => null,
        ],
    ],
];
```

Setting

```sh
MY_SERVICE_OPTION_NAME="option value" composer du
```

will give us

```php
return [
    'my-service.option-name' => "option value",
    'db' => [
        'pgsql' => [
            'password' => null,
        ],
    ],
];
```

Using

```sh
DB_PGSQL_PASSWORD="secret" composer du
```

will give us

```php
return [
    'my-service.option-name' => null,
    'db' => [
        'pgsql' => [
            'password' => "secret",
        ],
    ],
];
```

### Loading environment variables from .env files

There's an ability to load environment variables at build time from one or multiple `.env` files. That could simplify
development. In order to do it, additional package is required:

```
composer require --dev vlucas/phpdotenv
``` 

Then you can specify files to load variables from right in `composer.json`:

```json
    "extra": {
        "config-plugin": {
            "envs": [
                ".env"
            ],
```

Paths are relative to project root. Note that real environment variables have more priority than variables loaded from
`.env` files.

## Known issues

This plugin treats configs as simple PHP arrays. No specific
structure or semantics are expected and handled.
It is simple and straightforward, but I'm in doubt...
What about errors and typos?
I think about adding config validation rules provided together with
plugins. Will it solve all the problems?

Anonymous functions must be used in multiline form only:

```php
return [
    'works' => static function () {
        return 'value';
    },
    // this will not work
    'noway' => static function () {
        return 'value';
    },
];
```

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2016-2020, HiQDev (http://hiqdev.com/)
Copyright © 2020, Yiisoft (https://www.yiiframework.com/)
