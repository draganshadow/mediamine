<?php

namespace MediaMine\Initializer;

interface EntityManagerAware
{
    public function getEm();
    public function setEm($m);
}
