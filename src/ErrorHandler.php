<?php

namespace e96\sentry;

use yii\base\Component;

/**
 * @property $client \Raven_Client
 */
class ErrorHandler extends Component
{
    /**
     * @var string Sentry DSN
     */
    public $dsn;

    /**
     * @var array Raven client options.
     * @see \Raven_Client::__construct for more details
     */
    public $clientOptions = [];

    /**
     * @var \Raven_Client
     */
    protected $client;

    /**
     * @var \Raven_ErrorHandler
     */
    protected $ravenErrorHandler;

    /**
     * @var callable|null
     */
    protected $oldExceptionHandler;

    public function init()
    {
        parent::init();

        $this->client = new \Raven_Client($this->dsn, $this->clientOptions);

        $this->ravenErrorHandler = new \Raven_ErrorHandler($this->client);
        $this->ravenErrorHandler->registerErrorHandler(true);
        $this->ravenErrorHandler->registerShutdownFunction();
        $this->oldExceptionHandler = set_exception_handler(array($this, 'handleYiiExceptions'));
    }

    public function handleYiiExceptions($e)
    {
        if (!$e instanceof \yii\base\ErrorException) {
            $e->event_id = $this->client->getIdent($this->client->captureException($e));
        }

        if ($this->oldExceptionHandler) {
            call_user_func($this->oldExceptionHandler, $e);
        }
    }

    /**
     * @return \Raven_Client
     */
    public function getClient()
    {
        return $this->client;
    }
}