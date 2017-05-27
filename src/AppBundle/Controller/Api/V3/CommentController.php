<?php
namespace AppBundle\Controller\Api\V3;

use CoreBundle\Controller\BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\BrowserKit\Request;

/**
 * @Route("/api/v3/")
 */
class CommentController extends BaseController
{
    /**
     * @Route("comment/{slug}", options = { "expose" = true }, name="patch_comment")
     * @Method({"PATCH"})
     */
    public function patchComment(Request $request){
//        $this->denyAccessUnlessGranted('edit',)
    }

}