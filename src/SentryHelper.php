<?php

namespace e96\sentry;


class SentryHelper 
{
    /**
     * @param mixed $data
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function extraData($data)
    {
        /** @var ErrorHandler $raven */
        $raven = \Yii::$app->get('raven', false);
        if ($raven instanceof ErrorHandler) {
            $raven->client->extra_context($data);
            return true;
        }

        return false;
    }

    /**
     * @param string $message
     * @param null|\Exception $previousException
     * @param string $exceptionClass
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function captureWithMessage($message, $previousException = null, $exceptionClass = 'yii\base\Exception')
    {
        /** @var ErrorHandler $raven */
        $raven = \Yii::$app->get('raven', false);
        if ($raven instanceof ErrorHandler) {
            $raven->client->captureException(new $exceptionClass($message, 0, $previousException));
            return true;
        }

        return false;
    }

    public static function captureException($exception)
    {
        /** @var ErrorHandler $raven */
        $raven = \Yii::$app->get('raven', false);
        if ($raven instanceof ErrorHandler) {
            $raven->client->captureException($exception);
            return true;
        }

        return false;
    }
}
