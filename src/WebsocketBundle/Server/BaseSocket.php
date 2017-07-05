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

class BaseSocket extends Controller
{
    protected function seralizeToJson($type, $origin, $payload, $groups = null)
    {
        /** @var \FOS\RestBundle\Serializer\JMSSerializerAdapter  $serializer */
        $serializer = $this->get('fos_rest.serializer');
        $context = new \FOS\RestBundle\Context\Context();
        $context->setGroups($groups);

        return $serializer->serialize(array_merge([
            'type' => $type,
            'origin' => $origin
        ],$payload),'json',$context);
    }


}