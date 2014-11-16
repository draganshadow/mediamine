<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("mediamine.service.setting")
 */
class SettingService extends AbstractService
{
    public function getSetting($group, $key = false)
    {

        //TODO ADD CACHE
        $queryParams = array(
            'hydrate'  => Query::HYDRATE_ARRAY,
            'groupKey' => $group
        );
        if ($key) {
            $queryParams['key'] = $key;
        }
        $results = $this->getRepository('System\Setting')->findFullBy($queryParams);

        $settings = null;
        if (count($results)) {
            if (!$key) {
                $settings = array();
                foreach ($results as $result) {
                    $settings[$result['key']] = $result['value'];
                }
            } else {
                $settings = $results[0]['value'];
            }
        }
        return $settings;
    }
}