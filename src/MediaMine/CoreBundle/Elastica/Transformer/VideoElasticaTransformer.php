<?php

namespace MediaMine\CoreBundle\Elastica\Transformer;

use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\Video\Video;

/**
 * @Service("mediamine.elastica.transformer.video")
 */
class VideoElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * Transforms a video into an elastica object having the required keys
     *
     * @param object $video
     * @param array $fields
     * @return Document
     */
    public function transform($video, array $fields)
    {
        /**
         * @var $video Video
         */
        $identifier = $video->getId();
        $values = $video->getArrayCopy(0);
        $document = new Document($identifier,$values);
        return $document;
    }
}