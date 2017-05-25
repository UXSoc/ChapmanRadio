<?php
namespace DashboardBundle\Controller\Api\V1;

use CoreBundle\Controller\BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/")
 */
class DashShowController extends BaseController
{

    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("show/{name}",
     *     options = { "expose" = true },
     *     name="shows")
     * @Method({"PATCH"})
     */
    public function patchModifyShowAction(){

    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("show/{name}",
     *     options = { "expose" = true },
     *     name="shows")
     * @Method({"PATCH"})
     */
    public function deleteModfiyShowAction(){

    }

    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("show", options = { "expose" = true }, name="shows")
     * @Method({"POST"})
     */
    public function postCreateShowAction() {

    }

}