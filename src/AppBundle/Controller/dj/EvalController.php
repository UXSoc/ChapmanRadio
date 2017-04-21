<?php
namespace AppBundle\Controller\dj;
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

use ChapmanRadio\Evals;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EvalController extends Controller
{

    /**
     * @Route("/dj/eval", name="dj_eval")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("New Peer Evaluation");
        Template::RequireLogin("DJ Account");

        Template::js("/dj/js/evals.js");
        Template::js("/js/jquery.scrollTo.js");
        Template::css("/dj/css/evals.css");

        Template::SetBodyHeading("DJ Resources", "New Peer Evaluation");
        Template::AddBodyContent("
	<div id='moreinfo'><div class='head'>More Info</div><div id='moreinfoInner'></div></div>
	<div id='loading'><img src='/img/misc/loading.gif' alt='' /><br />Loading...</div>
	<div id='noshow' style='display:none;'>
		<b>No current broadcast.</b><br />
		It doesn't look like there are any shows broadcasting right now. Try back later.<br /><br />
		<a href='javascript:evals.load();'>&raquo; Check Again</a>
	</div>
	<div id='expiredshow' style='display:none;'>
		<b>No longer on air.</b><br />The show you are evaluating is no longer broadcasting.<br />
		You can leave more comments if you'd like. When you're done, you can <a href='javascript:evals.reset();'>move on</a> to evaluate the next show.<br />
		<a href='javascript:evals.reset();'>&raquo; Move On</a>
	</div>
	<div class='evals' style='display:none; overflow: auto;'>");

        $categories = Evals::categories(false);
        foreach ($categories as $catType => $categorydata) {
            Template::AddBodyContent("<div id='list$catType' class='list'>");
            if ($catType == 'good')
                Template::AddBodyContent("<div class='head' title='Well Done'><img src='/img/icons/smileys/happy48.png' alt='' /> Well<br />Done</div><div class='buttons'>");
            else
                Template::AddBodyContent("<div class='head' title='Needs Improvement'><img src='/img/icons/smileys/sad48.png' alt='' /> Needs<br />Improvement</div><div class='buttons'>");

            foreach ($categorydata as $value => $cat) {
                switch ($cat['type']) {
                    case 'button':
                        Template::AddBodyContent("<button class='eval-button' onmouseover='evals.moreinfo(\"$catType\",\"$value\");' onclick='evals.submit(\"$catType\",\"button\",\"$value\")' id='button-$value' title=\"" . htmlentities($cat['description']) . "\"><table><tr><td style='width:75px;'><img src='$cat[icon]' alt='' /></td><td style='vertical-align:middle;'>$cat[label]</td></tr></table></button>\n");
                        break;
                    case 'comment':
                        $icon = $catType == 'good' ? "/img/icons/chat50.png" : "/img/icons/chatbw50.png";
                        Template::AddBodyContent("<div class='comment gloss'>
					<h2>" . ($catType == 'good' ? "Compliment" : "Constructive Criticism") . "</h2>
					<div class='submit' style='background:url($icon);'>
						<button id='{$catType}commentbutton' onclick='evals.submit(\"$catType\",\"comment\",$(\"#{$catType}comment\").val());'>&gt;</button>
					</div>\n
					<textarea id='{$catType}comment' class='comment'></textarea><br />
				</div>");
                        break;
                }
            }
            Template::AddBodyContent("</div></div>");
        }

        Template::AddBodyContent("<div id='currentshowcontainer'>
	<h2>Now Playing</h2>
	<div id='currentshow' class='gloss'></div>
	<div id='currenteval'></div>
	</div>");
        Template::AddBodyContent("</div>");

        Template::script("if(typeof evals == 'undefined') evals = {}; ");
// send categories data to javascript
        Template::script("evals.categories = " . json_encode($categories) . ";");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}