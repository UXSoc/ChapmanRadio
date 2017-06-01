<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/31/17
 * Time: 6:58 AM
 */

namespace RestfulBundle\Controller;


use CoreBundle\Controller\BaseController;

use CoreBundle\Entity\Image;
use CoreBundle\Helper\ErrorWrapper;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\ImageRepository;
use CoreBundle\Service\ImageUploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController  extends BaseController
{
    /**
     * @Route("/image/{token}",
     *      options = { "expose" = true },
     *      name="get_image")
     * @Method({"GET"})
     */
    public function imageAction(Request $request,$token)
    {
        /** @var ImageUploadService $imageUploadService */
        $imageUploadService = $this->get('core.image_upload_service');

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->get('core.image_repository');


        /** @var Image $image */
        $image = $imageRepository->getImageByToken($token);
        if($image == null)
            return $this->restful([new WrapperNormalizer()],new ErrorWrapper("Unknown image"),410);

        return new BinaryFileResponse( $imageUploadService->getTargetDir().'/'. $imageUploadService->getImagePath($image));
    }
}