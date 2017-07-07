<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/6/17
 * Time: 4:54 PM
 */

namespace RestfulBundle\Controller\Api\V3;


use CoreBundle\Entity\Genre;
use CoreBundle\Repository\GenreRepository;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v3/")
 */
class GenreController extends FOSRestController
{
    /**
     * @Rest\Get("genre",
     *     options = { "expose" = true },
     *     name="get_genres")
     */
    public function getTagsAction(Request $request)
    {
        /** @var GenreRepository $genreRepository */
        $genreRepository = $this->getDoctrine()->getManager()->getRepository(Genre::class);
        $genres = $genreRepository->findGenre($request->get('search', ''));

        return $this->view(["categories" => $genres]);

    }
    /**
     * @Rest\Get("genre/{name}",
     *     options = { "expose" = true },
     *     name="get_genre")
     */
    public function getTagAction(Request $request,$name)
    {
        /** @var GenreRepository $genreRepository */
        $genreRepository = $this->getDoctrine()->getManager()->getRepository(Genre::class);

        /** @var Genre $genre */
        if($genre = $genreRepository->getGenre($name))
            return $this->view($genre->getGenre());
        throw $this->createNotFoundException("Genre Not Found");
    }

}