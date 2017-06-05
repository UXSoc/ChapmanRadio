<?php
namespace RestfulBundle\Controller\Api\V3;

use BroadcastBundle\Entity\Stream;
use CoreBundle\Controller\BaseController;
use CoreBundle\Helper\RestfulEnvelope;
use CoreBundle\Helper\SuccessWrapper;
use CoreBundle\Normalizer\EventNormalizer;
use CoreBundle\Normalizer\StreamNormalizer;
use CoreBundle\Normalizer\WrapperNormalizer;
use CoreBundle\Repository\StreamRepository;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v3/")
 */
class StreamController  extends BaseController
{
    /**
     * @Route("stream",
     *     options = { "expose" = true },
     *     name="get_active_streams")
     * @Method({"GET"})
     */
    public function getAllActiveStreamsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var StreamRepository $streamRepository */
        $streamRepository =  $em->getRepository(Stream::class);
        $streams =  $streamRepository->findAll();

        return RestfulEnvelope::successResponseTemplate("",$streams,
            [new StreamNormalizer(),new EventNormalizer()])->response();
    }

    /**
     * @param Request $request
     * @Route("stream/main",
     *     options = { "expose" = true },
     *     name="get_main_streams")
     * @Method({"GET"})
     */
    public function getMainStream(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var StreamRepository $streamRepository */
        $streamRepository =  $em->getRepository(Stream::class);
        $streams = $streamRepository->getStreamsTiedToEvent();

        return RestfulEnvelope::successResponseTemplate(null,$streams,
            [new EventNormalizer(),new StreamNormalizer()])->response();
    }

}
