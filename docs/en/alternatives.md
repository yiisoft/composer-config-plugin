# Alternative Sets of Configurations

Composer Config Plugin supports alternative sets of configurations creation on top of base config.
This allows you to build configurations of several applications within one project, separating the common part.

## How it works??

Main set of configurations merges with alternative, build and writes in separate folder named by alternative configuration name.

## Configuration

Define alternative sets in `config-plugin-alternatives` extra-option as follow: 
 
```
"extra": {
    "config-plugin": {
        "params": "config/params.php",
        "web": "config/web.php",
        ...
    },
    "config-plugin-alternatives": {
        "alfa": {
            "params": "config/alfa/params.php",
            "main": [
                "$web"
                "config/alfa/main.php"
            ]
        },
        "beta": {
            "main": "config/beta/main.php
        }
    }
},
```
As shown, in alternative sets you can use configurations to merge from both the current one, 
and main set. (in given example, in set `alfa` we use `$web` configuration from the main set).

To load sets of alternative configurations in your app, in entry point (`index.php`), for example, use `require`:

```php
$config = require Yiisoft\Composer\Config\Builder::path('alfa/main');
 ```

## `$params` variable in alternative sets.

If defined in alternative set, `params` will be merged with `params` from the main set,
and can be used in building all configurations in term of current set.

## Example

Extra-options of `composer.json`:

```
"extra": {
    "config-plugin": {
        "params": "config/params.php",
        "web": "config/web.php",
        "providers": "config/providers.php"
    },
    "config-plugin-alternatives": {
        "alfa": {
            "params": "config/alfa/params.php",
            "main": [
                "$web"
                "config/alfa/main.php"
            ],
            "providers: "config/alfa/providers.php"
        }
    }
},
```

Built configurations in `vendor/yiisoft/composer-config-plugin-output/`:

```
# Main set
/params.php
/web.php
/providers.php

# alternative set 'alfa'
/alfa/params.php
/alfa/web.php
/alfa/main.php
/alfa/providers.php
```

# Explain of the results:

Alternative set `alfa`:

- `params.php` array  will have `params` from the main set, merged with `config/alfa/params.php`.
   built `params.php` will be used in assembly of all configurations of the `alfa` set.   
- `/alfa/web.php` Configuration will be taken from main set.
- `/alfa/main.php` Configuration will be built from main set `web` configuration and `config/alfa/main.php`.
- `/alfa/providers.php` Configuration will be built from `providers` configuration of main set and `config/alfa/providers.php`.
