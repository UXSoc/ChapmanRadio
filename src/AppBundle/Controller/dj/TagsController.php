<?php

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagsController extends Controller
{

    /**
     * @Route("/dj/tags", name="dj_tags")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Create your own Tags - DJ Resources");
        //Template::RequireLogin("/dj/tags", "DJ Resources");

        $dirs = array(
            "Noise" => PATH . "resources/tags/noise/",
            "Craig's Voiceover" => PATH . "resources/tags/craig/",
            "Sample Music Beds" => PATH . "resources/tags/beds/",
            "Novelty Audio Files" => PATH . "resources/tags/novelty/",
            "Sound Effects" => PATH . "resources/tags/sfx/",
            "Computer Voice" => PATH . "resources/tags/voice/",
        );

        $meta = array();
        $data = file_get_contents(PATH . "dj/tags.data.txt");

        $items = explode("`", $data);
        foreach ($items as $item) {
            if (!$item) continue;
            $rows = explode("\n", $item);
            $key = trim(array_shift($rows));
            $newrows = array();
            foreach ($rows as $k => $v) if (trim($v)) $newrows[$k] = trim($v);
            $meta[$key] = $newrows;
        }

        Template::SetBodyHeading("DJ Resources", "Create your own Tags");
        Template::AddBodyContent("
<p>Here is a collection of resources available for download to create your own show tags.</p>
	<div style='clear: both;'>
		<div class='specs' style='float: left; width: 45%; margin: 10px;'>
			<a href='http://audacity.sourceforge.net/download/' target='_blank'>Download Audacity</a> if you need an application to create your tags.<br />
			<a href='/resources/tags/craig.zip'>Download Craig's Voiceover</a> if you want the full collection, or download individual mp3s below.
		</div>
		<div class='specs' style='float: left; width: 45%; margin: 10px;'>
			<h2>Form</h2>
			<p>Start and end with a noise sample<br />Add a music bed in the background<br />Record a voiceover on top</p>
		</div>
	</div>");

        $total = 0;
        foreach ($dirs as $eng => $dir) {
            $d = dir($dir);

            Template::AddBodyContent("<div style='clear: both; overflow: auto;'><h2 style='text-align: left; text-transform: uppercase; font-size: 19px; color: #79C043;'>$eng</h2>");
            $count = 0;
            $col = array("", "", "");
            while (false !== ($entry = $d->read())) {

                if (substr($entry, 0, 1) == ".") continue;
                $name = isset($meta[$entry]) ? $meta[$entry][0] : $entry;
                $info = "";

                if (isset($meta[$entry])) foreach ($meta[$entry] as $key => $val) if ($key != 0) $info .= "<br />$val<br />";
                else $info = "$entry";

                $col[$count++ % 3] .= "<div style='border: 1px solid #CCC; margin: 5px; padding: 5px;'><h3>$name</h3><br /><p style='text-align:left;'>$info</p>" . self::mp3player($dir . $entry) . download($dir . $entry, $entry) . "</div>";
            }
            $d->close();
            foreach ($col as $c) Template::AddBodyContent("<div style='float:left; width: 310px;'>" . $c . "</div>");
            Template::AddBodyContent("</div>");
        }

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container,"</tr></table>"));

    }

    function mp3player($mp3)
    {
        $mp3 = urlencode($mp3);
        return "<object type='application/x-shockwave-flash' data='/plugins/flashmp3player/player_mp3_maxi.swf' width='200' height='20'>
        <param name='movie' value='/plugins/flashmp3player/player_mp3_maxi.swf' />
        <param name='bgcolor' value='#ffffff' />
        <param name='FlashVars' value='mp3=$mp3' />
        </object>";
    }

    function download($mp3, $entry)
    {
        return "<div class='gloss' style='margin:10px 30px;display:block;width:200px;'>
            <a href='$mp3' style='display:block;width:100%;height:100%;' onmouseover='this.style.background=\"rgba(0,0,0,.2)\";' onmouseout='this.style.background=\"transparent\"'>
                <img src='/img/misc/download.png' alt='' style='float: left; width: 40px; margin-top: -10px;'/>
                Download
            </a>
	    </div>";
    }
}
