<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\EntityRepository;

class CharacterRepository extends EntityRepository
{
    public function createCharacter($video, $name) {
        $character = new Character();
        $character->name = $name;
        $character->video = $video;
        $this->getEntityManager()->persist($character);
        return $character;
    }
}