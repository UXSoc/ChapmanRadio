<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/19/17
 * Time: 9:25 PM
 */

namespace AppBundle\Controller\dj;

use ChapmanRadio\Season;
use ChapmanRadio\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DownloadsController extends Controller
{

    /**
     * @Route("/dj/downloads", name="dj_downloads")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Downloads - DJ Resources");
        Template::RequireLogin("/dj/downloads","DJ Account");
        $season = Season::current();
        Template::SetBodyHeading("DJ Resources", "Downloads: ".Season::Name());
        Template::AddBodyContent("<div class='leftcontent'><h3>Downloads for this Semester</h3><br />");

        if( !file_exists(PATH.'/downloads/'.$season) ) mkdir(PATH.'/downloads/'.$season);
        $downloads = array();
        $d = dir(PATH.'/downloads/'.$season);
        while (false !== ($entry = $d->read())) {
            if($entry == "." || $entry == ".." || $entry == ".DS_Store" || $entry[0] == "!") continue;
            $downloads[] = $entry;
        }

        $d->close();

        Template::style(".downloads {list-style:none;font-family:courier;width:400px;font-size:12px;margin:10px auto;text-align:left;} .downloads a { display:block;padding:6px 10px;border:1px solid #AAA;margin:2px 0;background:#F0F0F0;} .downloads a:hover { background:#DEDEDE; }");

        if(count($downloads) == 0)
            Template::AddBodyContent("<p style='width:400px;background:#EEE;margin:auto;padding:6px 12px;border:1px solid #848484;'>Sorry, there are no currently downloads for ".Season::Name()."</p>");

        Template::AddBodyContent("<ul class='downloads'>");

        foreach($downloads as $download) Template::AddBodyContent( "<li><a href='".(PATH."downloads/$season/$download")."'>$download</a></li>");
        Template::AddBodyContent("</ul>");

        Template::AddBodyContent("<h3>Show Tags</h3><p>Ready to create tags for your show? <a href='/dj/tags'>Get resources for making tags.</a></p>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("</div>"));
    }
}