<?php

namespace MediaMine\CoreBundle\Elastica\Hydrator;

use Doctrine\ORM\Query;
use Elastica\Document;
use FOS\ElasticaBundle\Doctrine\AbstractElasticaToModelTransformer;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\Tunnel\Person;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;


class ElasticaToPersonTransformer extends AbstractElasticaToModelTransformer
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * Transforms an array of elastica objects into an array of
     * model objects fetched from the doctrine repository
     *
     * @param array $elasticaObjects of elastica objects
     * @throws \RuntimeException
     * @return array
     **/
    public function transform(array $elasticaObjects)
    {
        $ids = $highlights = array();
        foreach ($elasticaObjects as $elasticaObject) {
            $ids[] = $elasticaObject->getId();
            $highlights[$elasticaObject->getId()] = $elasticaObject->getHighlights();
        }

        $objects = $this->findByIdentifiers($ids, $this->options['hydrate']);
//        if (!$this->options['ignore_missing'] && count($objects) < count($elasticaObjects)) {
//            throw new \RuntimeException('Cannot find corresponding Doctrine objects for all Elastica results.');
//        };
//
//        foreach ($objects as $object) {
//            if ($object instanceof HighlightableModelInterface) {
//                $object->setElasticHighlights($highlights[$object->getId()]);
//            }
//        }
//
//        // sort objects in the order of ids
//        $idPos = array_flip($ids);
//        $identifier = $this->options['identifier'];
//        $propertyAccessor = $this->propertyAccessor;
//        usort($objects, function($a, $b) use ($idPos, $identifier, $propertyAccessor)
//        {
//            return $idPos[$propertyAccessor->getValue($a, $identifier)] > $idPos[$propertyAccessor->getValue($b, $identifier)];
//        });

        return $objects;
    }

    /**
     * Fetches objects by theses identifier values
     *
     * @param array $identifierValues ids values
     * @param Boolean $hydrate whether or not to hydrate the objects, false returns arrays
     * @return array of objects or arrays
     */
    protected function findByIdentifiers(array $identifierValues, $hydrate)
    {
        if (empty($identifierValues)) {
            return [];
        }
        $params = [
            'hydrate' => $hydrate ? Query::HYDRATE_OBJECT : Query::HYDRATE_ARRAY,
            'addImages' => true,
            'id' => $identifierValues
        ];
        return $this->getRepository('Common\Person')->findFullBy($params);
    }
}