# Альтернативные наборы конфигураций

Composer Config Plugin поддерживает создание альтернативных наборов конфигураций на базе основного.
Это позволяет собрать конфигурации нескольких приложений в рамках одного проекта выделив общую часть.

## Как это работает?

Основной набор конфигураций объединяется с альтернативным, собирается и размещается в отдельной папке.

## Настройка

В extra-опции `config-plugin-alternatives` указываются альтернативные наборы в формате:
 
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

При настройке вы можете использовать для объединения конфигурации как из текущего,
так и из основного набора (например, в наборе `alfa` используется конфигурация `$web` из основного набора).

Для загрузки альтернативных конфигураций в вашем приложении используйте `require`:

```php
$config = require Yiisoft\Composer\Config\Builder::path('alfa/main');
 ```

## Параметры `$params` в альтернативных наборах

Если в альтернативном наборе вы укажете параметры `params`, то они будут объединены с `params` 
из основного набора и использованы при сборке всех конфигураций в рамках текущего набора.

## Пример

Extra-опции в `composer.json`:

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

Собранные конфигурации в `vendor/yiisoft/composer-config-plugin-output/`:

```
# Основной набор
/params.php
/web.php
/providers.php

# Альтернативный набор alfa
/alfa/params.php
/alfa/web.php
/alfa/main.php
/alfa/providers.php
```

### Объяснение результата

Альтернативный набор `alfa`:

- Для сборки всех конфигураций набора будут использованы параметры из основного набора, объединённые с `config/alfa/params.php`.
- Конфигурация `/alfa/web.php` будет взята из основного набора.
- Конфигурация `/alfa/main.php` будет собрана из конфигурации `web` из основного набора и `config/alfa/main.php`.
- Конфигурация `/alfa/providers.php` будет собрана из конфигурации `providers` из основного набора и `config/alfa/providers.php`.
