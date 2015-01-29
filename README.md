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
You can provide additional information with exceptions:
```php
/** @var ErrorHandler $raven */
$raven = \Yii::$app->get('raven');
$raven->client->extra_context($task->attributes);

throw new Exception('unknown task type');
```

## Rich logging through built in Yii2 logging methods

```php
try{
    $model1->save();
}catch (\Exception $e){
    Yii::warning([
        'msg' => 'MsrpAddExceptionSentryTest', // this is for the text msg
        'data' => [ // additional data sent as 'extra_data'
            'userId' => Yii::$app->user->id,
            'action' => 'MsrpAddExceptionSentryTest',
        ],
        'exception' => $e->getTrace(), // to get the native Sentry stacktrace feature available
    ], 'commerceMsrp');
}
```