<?php

namespace e96\sentry;

use yii\base\Component;
use yii\base\ErrorException;

/**
 * @property \Raven_Client $client
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
        // shutdown function not working in yii2 yet: https://github.com/yiisoft/yii2/issues/6637
        //$this->ravenErrorHandler->registerShutdownFunction();
        $this->oldExceptionHandler = set_exception_handler(array($this, 'handleYiiExceptions'));
    }

    /**
     * @param \Exception $e
     */
    public function handleYiiExceptions($e)
    {
        if ($this->canLogException($e)) {
            $e->event_id = $this->client->getIdent($this->client->captureException($e));
        }

        if ($this->oldExceptionHandler) {
            call_user_func($this->oldExceptionHandler, $e);
        }
    }

    /**
     * Filter exception and its previous exceptions for yii\base\ErrorException
     * Raven expects normal stacktrace, but yii\base\ErrorException may have xdebug_get_function_stack
     * @param \Exception $e
     * @return bool
     */
    public function canLogException(&$e)
    {
        if (function_exists('xdebug_get_function_stack')) {
            if ($e instanceof ErrorException) {
                return false;
            }

            $selectedException = $e;
            while ($nestedException = $selectedException->getPrevious()) {
                if ($nestedException instanceof ErrorException) {
                    $ref = new \ReflectionProperty('Exception', 'previous');
                    $ref->setAccessible(true);
                    $ref->setValue($selectedException, null);
                    return true;
                }
                $selectedException = $selectedException->getPrevious();
            }
        }

        return true;
    }

    /**
     * @return \Raven_Client
     */
    public function getClient()
    {
        return $this->client;
    }
}