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
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\ImageRepository;
use CoreBundle\Service\ImageUploadService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\FOSRestBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController  extends FOSRestController
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
        $imageUploadService = $this->get(ImageUploadService::class);

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->get(Image::class);

        /** @var Image $image */
        $image = $imageRepository->getImageByToken($token);
        if($image == null)
            throw  $this->createNotFoundException('Image Not Found');

        return new BinaryFileResponse( $imageUploadService->getTargetDir().'/'. $imageUploadService->getImagePath($image));
    }
}
