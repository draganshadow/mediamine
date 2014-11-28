<?php
namespace MediaMine\CoreBundle\Message;

use PhpAmqpLib\Message\AMQPMessage;

class AbstractMessage {

    /**
     * @var AMQPMessage
     */
    private $amqpMessage;

    /**
     * @var array
     */
    private $bodyArray;

    public function exchangeAMQP(AMQPMessage $amqpMessage) {
        $this->amqpMessage = $amqpMessage;
        $this->bodyArray = json_decode($this->amqpMessage->body, true);
        $this->exchangeArray($this->bodyArray);
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $array = get_object_vars($this);
        unset($array['amqpMessage']);
        unset($array['bodyArray']);
        return $array;
    }

    /**
     * @param $array
     */
    public function exchangeArray($array)
    {
        if (is_array($array)) {
            foreach($array as $key => $value)
            {
                if (property_exists($this,$key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function serialize() {
        return json_encode($this->getArrayCopy());
    }
}