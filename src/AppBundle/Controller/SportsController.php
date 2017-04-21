<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/19/17
 * Time: 9:59 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;


class SportsController extends Controller
{

    /**
     * @Route("/sports", name="sports")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageSection("/sports");
        Template::SetPageTitle("SportsController");

        Template::SetBodyHeading("Chapman Radio", "SportsController");

        Template::style(".h3min {width:400px;margin:auto;}");
        Template::css("/css/feeds.css");
        Template::js("/js/reflex.js");

//$temp = DB::GetFirst(" SELECT COUNT( * ) AS total FROM `mp3s` WHERE showid = ".SPORTS_ARCHIVES_SHOWID);

        Template::AddBodyContent("<div class='couju-info' style='margin: 10px; padding: 10px; display: inline-block; font-size: 16px; width: 95%;'>Interested in becoming a play-by-play announcer? Contact Matthew Brown at <a href='mailto:sports@chapmanradio.com'>sports@chapmanradio.com</a>.</div>");

        Template::AddBodyContent("
        <div class='leftcontent'>
        <p>ChapmanRadio.com also broadcasts many Panther sports games with live, play-by-play commentary. Chapman Radio commentators are committed to creating a professional on-air broadcast complete with half-time coach interviews and a pre-game report, which is why sports on ChapmanRadio.com continues to be one of the station's most popular aspects.</p>
        <p>If there is a live broadcast, you'll be able to <a href='/listenlive?stream=sports' onclick='return openListenLive(\"sports\")'>listen to it here</a>.</p>
        </div>
        
        <p><br class='clear' /></p>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}