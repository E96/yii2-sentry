Yii2 sentry client
=================

##Install
```
php composer.phar require e96/yii2-sentry:dev-master
```

## Usage
In config file:

```php
'bootstrap' => ['log', 'raven'],
'components' => [
    'raven' => [
        'class' => 'e96\sentry\ErrorHandler',
        'dsn' => '', // Sentry DSN
    ],
    'log' => [
        'targets' => [
            [
                'class' => 'e96\sentry\Target',
                'levels' => ['error', 'warning'],
                'dsn' => '', // Sentry DSN
            ]
        ],
    ],
]
```
