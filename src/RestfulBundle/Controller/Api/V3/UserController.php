<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/27/17
 * Time: 6:14 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use CoreBundle\Entity\User;
use CoreBundle\Event\ImageEvent;
use CoreBundle\Event\ImageRetrieveEvent;
use CoreBundle\Events;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/v3/")
 */
class UserController extends FOSRestController
{

}