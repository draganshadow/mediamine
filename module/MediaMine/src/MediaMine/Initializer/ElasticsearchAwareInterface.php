<?php

namespace MediaMine\Initializer;

interface ElasticsearchAwareInterface
{
    /**
     * @return \Elastica\Client
     */
    public function getElasticsearch();

    /**
     * @param \Elastica\Client $es
     * @return mixed
     */
    public function setElasticsearch(\Elastica\Client $es);
}
