<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/4/17
 * Time: 10:39 PM
 */

namespace WebsocketBundle\Server;



use JMS\Serializer\SerializerBuilder;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WebsocketBundle\Server\Packets\Packet;

class BaseSocket extends Controller
{
    protected function serializePacket(Packet $packet)
    {
        /** @var \FOS\RestBundle\Serializer\JMSSerializerAdapter  $serializer */
        $serializer = $this->get('fos_rest.serializer');
        $context = new \FOS\RestBundle\Context\Context();
        $context->setGroups($packet->groups());

        $result = $packet->getPayload();
        $result['type'] = $packet->getType();

        return $serializer->serialize($result,'json',$context);
    }


    protected function seralizeToJson($payload, $groups = null)
    {
        /** @var \FOS\RestBundle\Serializer\JMSSerializerAdapter  $serializer */
        $serializer = $this->get('fos_rest.serializer');
        $context = new \FOS\RestBundle\Context\Context();
        $context->setGroups($groups);

        return $serializer->serialize($payload,'json',$context);
    }


}