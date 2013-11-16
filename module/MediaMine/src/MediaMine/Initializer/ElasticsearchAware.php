<?php

namespace MediaMine\Initializer;

interface ElasticsearchAware
{
    public function getEs();
    public function setEs($es);
}
