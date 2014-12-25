<?php

namespace MediaMine\CoreBundle\Elastica\Transformer;

use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\Common\Person;

/**
 * @Service("mediamine.elastica.transformer.person")
 */
class PersonElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * Transforms a person into an elastica object having the required keys
     *
     * @param object $person
     * @param array $fields
     * @return Document
     */
    public function transform($person, array $fields)
    {
        /**
         * @var $person Person
         */
        $identifier = $person->getId();
        $values = $person->getArrayCopy(0);
        $document = new Document($identifier,$values);
        return $document;
    }
}