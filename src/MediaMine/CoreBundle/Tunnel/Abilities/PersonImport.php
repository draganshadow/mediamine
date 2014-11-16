<?php
namespace MediaMine\Core\Tunnel\Abilities;

interface PersonImport
{
    /**
     * Take id of Common\Person and create Tunnel Person
     *
     * @param $id
     * @return \MediaMine\Core\Entity\Tunnel\Person
     */
    public function importPerson($id);
}