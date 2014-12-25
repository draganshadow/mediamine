<?php

namespace MediaMine\CoreBundle\Elastica\Transformer;

use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\Video\Group;

/**
 * @Service("mediamine.elastica.transformer.group")
 */
class GroupElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * Transforms a group into an elastica object having the required keys
     *
     * @param object $group
     * @param array $fields
     * @return Document
     */
    public function transform($group, array $fields)
    {
        /**
         * @var $group Group
         */
        $identifier = $group->getId();
        $values = $group->getArrayCopy(0);
        $document = new Document($identifier,$values);
        return $document;
    }
}