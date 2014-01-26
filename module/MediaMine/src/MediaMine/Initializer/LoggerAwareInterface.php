<?php
namespace MediaMine\Initializer;

interface LoggerAwareInterface
{
    /**
     * Set the logger
     *
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(\Zend\Log\Logger $logger);

    /**
     * Get the logger
     *
     * @return \Zend\Log\Logger
     */
    public function getLogger();
}
