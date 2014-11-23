<?php
namespace MediaMine\CoreBundle\Message\System;

use MediaMine\CoreBundle\Message\AbstractMessage;

class Job extends AbstractMessage {

    public $groupKey;

    public $key;

    public $service;

    public $jobId;

    public $parentJobService;

    public $parentJobId;

    public $parameters;
}