<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/30/17
 * Time: 2:47 PM
 */

namespace RestfulBundle\Controller\Api\V3\Secure;

use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Tag;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\TagNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TagController  extends BaseController
{

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/tag/{tag}", options = { "expose" = true }, name="put_tag")
     * @Method({"PUT"})
     */
    public function putTagAction($tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get('core.tag_repository');
        $result = $tagRepository->getTag($tag);
        if($result  != null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Tag Already Exist"), 410);

        $result = new Tag();
        $result->setTag($tag);

        $em->persist($result);
        $em->flush();
        return $this->restful([new WrapperNormalizer(),new TagNormalizer()],new SuccessWrapper($result,"Tag added"));

    }

    /**
     * @Security("has_role('ROLE_STAFF')")
     * @Route("/tag/{tag}", options = { "expose" = true }, name="delete_tag")
     * @Method({"DELETE"})
     */
    public function deleteTagAction($tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->get('core.tag_repository');
        $result = $tagRepository->getTag($tag);
        if($result  == null)
            return $this->restful([new WrapperNormalizer()], new ErrorWrapper("Tag does not exist"), 410);

        $em->remove($result);
        $em->flush();
        return $this->restful([new WrapperNormalizer(),new TagNormalizer()],new SuccessWrapper($result,"Tag was Removed"));
    }

}