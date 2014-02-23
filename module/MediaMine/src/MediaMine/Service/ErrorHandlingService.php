<?php
namespace MediaMine\Service;

use Netsyos\Common\Initializer\LoggerAwareInterface;

class ErrorHandlingService implements LoggerAwareInterface
{
    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    function logException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:n" . implode("n", $messages);
        $log .= "nTrace:n" . $trace;

        $this->logger->err($log);
    }

    function logEvent($event)
    {
        $this->logger->err(print_r($event, true));
    }

    function logMessage($m)
    {
        $this->logger->err($m);
    }

    /**
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(\Zend\Log\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}