<?php
namespace MediaMine\CoreBundle\Message\System;

use MediaMine\CoreBundle\Message\AbstractMessage;

class Task extends AbstractMessage {

    public $service;

    public $method = 'execute';

    public $jobService;

    public $jobId;

    public $parameters;
}