<?php
namespace MediaMine\CoreBundle\Shared;

trait LoggerAware {

    /**
     * @var \Monolog\Logger
     * @Inject("logger")
     */
    public $logger;

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
} 