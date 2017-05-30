<?php
namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/private")
 */
class ShowController extends BaseController
{

    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("show/{name}",
     *     options = { "expose" = true },
     *     name="patch_show")
     * @Method({"PATCH"})
     */
    public function patchModifyShowAction(){

    }


    /**
     * @Security("has_role('ROLE_DJ') | has_role('ROLE_STAFF')")
     * @Route("show/{name}",
     *     options = { "expose" = true },
     *     name="delete_show")
     * @Method({"PATCH"})
     */
    public function deleteModfiyShowAction(){

    }

    /**
     * @Security("has_role('ROLE_DJ')")
     * @Route("post_show", options = { "expose" = true }, name="shows")
     * @Method({"POST"})
     */
    public function postCreateShowAction() {

    }

}