<?php
namespace MediaMine\Repository\Video;

use Netsyos\Common\Repository\EntityRepository;
use MediaMine\Entity\Video\Genre;

class GenreRepository extends EntityRepository
{
    public function createGenre($name) {
        $genre = new Genre();
        $genre->name = strtolower($name);
        $this->getEntityManager()->persist($genre);
        return $genre;
    }

    public function findFullBy($name = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('Genre')
            ->from('MediaMine\Entity\Video\Genre','Genre');
        if ($name != null) {
            $qb->andwhere('Genre.name = :name');
            $params['name'] = strtolower($name);
        }
        $genres = $qb->setParameters($params)->getQuery()->getResult();
        return $genres;
    }
}