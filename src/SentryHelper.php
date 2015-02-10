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
     * @param string $level one of Raven_Client::* levels
     * @param string $exceptionClass
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function captureWithMessage($message, $previousException = null, $level = \Raven_Client::ERROR, $exceptionClass = 'yii\base\Exception')
    {
        /** @var ErrorHandler $raven */
        $raven = \Yii::$app->get('raven', false);
        if ($raven instanceof ErrorHandler) {
            $raven->client->captureException(new $exceptionClass($message, 0, $previousException), ['level' => $level]);
            return true;
        }

        return false;
    }

    /**
     * @param \Exception $exception
     * @param string $level one of Raven_Client::* levels
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function captureException($exception, $level = \Raven_Client::ERROR)
    {
        /** @var ErrorHandler $raven */
        $raven = \Yii::$app->get('raven', false);
        if ($raven instanceof ErrorHandler) {
            $raven->client->captureException($exception, ['level' => $level]);
            return true;
        }

        return false;
    }
}
