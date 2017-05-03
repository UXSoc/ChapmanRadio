<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\NewsModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsController extends Controller
{

    /**
     * @Route("/dj/news", name="dj_news")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Class News");
        //Template::RequireLogin("/dj/news", "DJ Account");
        Template::Bootstrap();

        Template::Css("/legacy/css/page-news.css");

        Template::SetBodyHeading("Class News");
        Template::AddStaffAlert("You can post news here at <a href='/staff/classnews'>/staff/classnews</a>");

        $news = NewsModel::Paged(10);

        Template::AddBodyContent("<div class='page-news'>");

        if (empty($news)) Template::AddCoujuNotice("No news to display");
        else foreach ($news as $newsitem)
            Template::AddBodyContent("<article>
		<h2>{$newsitem->title}</h2>
		<span class='date'>Posted " . date("F jS, Y", $newsitem->posted_unix) . "</span>
		<p>{$newsitem->body}</p></article>");

        Template::AddBodyContent("</div>");

        return Template::Finalize($this->container);
    }
}