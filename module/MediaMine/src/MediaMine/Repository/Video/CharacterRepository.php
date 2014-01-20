<?php
namespace MediaMine\Repository\Video;

use Doctrine\ORM\EntityRepository;
use MediaMine\Entity\Video\Character;

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