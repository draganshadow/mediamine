<?php
namespace MediaMine\Service;

use Heartsentwined\CronExprParser\Parser;

class CronService extends AbstractService
{
    public function createCron($key, $frequency, $service, $callback, $arguments) {
        $fields = array();
        Parser::matchTime(time(), $frequency);
        $fields['key'] = $key;
        $fields['frequency'] = $frequency;
        $fields['service'] = $service;
        $fields['callback'] = $callback;
        $fields['arguments'] = $arguments;
        $cron = $this->getRepository('System\Cron')->createCron($fields);
        $this->flush(true);
    }

    public function run() {

    }
}