<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

namespace AppBundle\Controller\dj;


use AppBundle\Entity\User;
use ChapmanRadio\DB;
use function ChapmanRadio\error;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Podcast;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Stats;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShowsController extends Controller
{

    /**
     * @Route("/dj/shows", name="dj_shows")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("My Shows");
        //Template::RequireLogin("/dj/shows","DJ Resources");

        /** @var User $user */
        $user = $this->getUser();



        Template::css("/legacy/css/ui.css");
        Template::css("/legacy/css/formtable.css");
        Template::css("/legacy/css/stats.css");
        Template::css("/legacy/css/recording_data.css");
        Template::js("/legacy/dj/js/shows.js");
        Template::js("/legacy/js/jquery.color.js");

// is there something to process?
        $notify = "";
        if(isset($_POST['savepreferences'])) {
            $showid = @$_REQUEST['showid'] or error("Missing show id variable");
            if(!is_numeric($showid)) error("The show id # entered was not numeric");
            $showname = @$_REQUEST['showname'] or error("Please go back and <b>enter a show name</b>.");
            $genre = @$_REQUEST['genre'] or error("Please go back and <b>enter a genre</b>.");
            $description = @$_REQUEST['description'] or error("Please go back and <b>enter a description</b>.");
            $link = @$_REQUEST['link'] or '';
            if($link && !preg_match("/^https?:\\/\\//",$link) ) $link = "http://$link";
            $explicit = (@$_REQUEST['explicit']) ? '1' : '0';
            $podcastcategory = @$_REQUEST['podcastcategory'] or error("Please go back and <b>pick a podcast category</b>.");
            $podcastcategory = trim($podcastcategory);

            DB::Query("UPDATE shows SET
		showname = :name,
		genre = :genre,
		description = :description,
		explicit = :explicit,
		link = :link,
		podcastcategory = :podcastcategory
		WHERE showid = :id", array(
                ":id" => $showid,
                ":name" => $showname,
                ":genre" => $genre,
                ":description" => $description,
                ":link" => $link,
                ":explicit" => $explicit,
                ":podcastcategory" => $podcastcategory
            ));

            $notify = "<h2>Updated</h2><p style='color:green;'>Your changes to <b>".stripslashes($showname)."</b> have been <b>saved</b></p>";
            Template::script("if(typeof panes === 'undefined') panes = new Array(); panes[$showid] = 'preferences'");
        }
        if(isset($_POST['UPLOAD_TAG'])) {
            // validate upload
            $showid = @$_REQUEST['showid'] or error("Missing show id variable");
            if(!is_numeric($showid)) error("The show id # entered was not numeric");
            $label = @$_REQUEST['label'] or error("Please go back and <b>enter a label</b>.");
            if(!@$_FILES['file']['tmp_name']) error("Please go back and <b>pick a file from your computer</b> for upload.");
            if(@$_FILES['file']['error']) error("An error has occurred. Please try again or select a different file to upload.");
            $oktypes = array("audio/mpeg","audio/x-mpeg","audio/mp3","audio/x-mp3","audio/mpeg3","audio/x-mpeg3","audio/mpg","audio/x-mpg","audio/x-mpegaudio");
            if(!in_array($_FILES['file']['type'], $oktypes)) error("Sorry, but you tried to upload a file with the filetype: <b>{$_FILES['file']['type']}</b>, which is not allowed.<br />Please go back and upload an MP3 file.");
            if($_FILES['file']['size'] > 3145728) error("Sorry, but the file you uploaded is too large. There is a maximum of <b>3MB</b> for show tags, and the file you uploaded exceeded that limit. <br />Please go back and upload a smaller mp3 file.");
            // do the upload
            $now = time();
            $path = "mp3s/tags/";
            if(!file_exists(PATH.$path)) mkdir(PATH.$path);
            $path .= $showid."/";
            if(!file_exists(PATH.$path)) mkdir(PATH.$path);
            $path .= "$now-".preg_replace("/\\W/","", array_shift(explode(".",$_FILES['file']['name'])) ).".mp3";
            if(!move_uploaded_file($_FILES['file']['tmp_name'], PATH.$path)) error("An unexpected error occured while trying to upload the file. Please try again, or email webmaster@chapmanradio.com for help");

            DB::Insert("tags", [
                "showid" => $showid,
                "label" => $label,
                "url" => "/$path",
                "uploadedon" => $now
            ]);
            // finish
            $notify = "<h2>New Show Tag</h2><p style='color:green;'>You've just uploaded <b>".stripslashes($label)."</b>.</p>";
            Template::script("if(typeof panes === 'undefined') panes = new Array(); panes[$showid] = 'tags'");
        }
        if(isset($_POST['DELETE_TAG'])) {
            $showid = @$_REQUEST['showid'] or error("Missing show id variable");
            if(!is_numeric($showid)) error("The show id # entered was not numeric");
            $tagid = @$_REQUEST['tagid'] or error("Missing tag id variable");
            if(!is_numeric($tagid)) error("The tag id # entered was not numeric");
            $tag = DB::GetFirst("SELECT * FROM tags WHERE tagid=$tagid");
            if(!$tag) $notify = "<h2>Deleted</h2><p style='color:red;'>That tag has been deleted.</p>";
            else {
                $path = preg_replace("/^\\//", "", $tag['url']);
                if(!@unlink(PATH.$path)) error("An expected error has occured while attempting to delete <b>$path</b>. Please try again later, or email webmaster@chapmanradio.com for assistance.");
                DB::Query("DELETE FROM tags WHERE tagid=$tagid");
                $notify = "<h2>Deleted Show Tag</h2><p style='color:red;'>You have permanently deleted the tag <b>$tag[label]</b>.</p>";
            }
            Template::script("if(typeof panes === 'undefined') panes = new Array(); panes[$showid] = 'tags'");
        }
        if(isset($_POST['updatemp3'])) {

            $showid = Request::GetInteger('showid');
            if(!$showid) error("Missing show id variable");

            $mp3id = Request::GetInteger('mp3id');
            if(!$mp3id) error("Missing mp3 id variable");

            $label = Request::Get('label');
            if(!$label) error("Please go back and <b>enter a name for your recording</b>.");

            DB::Query("UPDATE mp3s SET label = :label, description = :desc, active = :active, clean=0 WHERE mp3id = :id", array(
                ":label" => $label,
                ":desc" => Request::Get('description'),
                ":active" => Request::GetBool('active'),
                ":id" => $mp3id
            ));

            $notify = "<h2>Updated</h2><p style='color:green;'>Your changes to <b>".stripslashes($label)."</b> have been <b>saved</b>.</p>";
            Template::script("if(typeof panes === 'undefined') panes = new Array(); panes[$showid] = 'recordings'");
        }

// Handle image uploads where JS failed
        try{
            if(Uploader::HandleAnyModel() !== NULL){
                $notify = "<h2>Updated</h2><p style='color:green;'>Your image has successfully been uploaded.</p>";
            }
        }
        catch(Exception $e){
            $notify = "<h2>Updated</h2><p style='color:#A00;'>".$e->GetMessage()."</p>";
        }

// Tab Generators
        $generate = Request::Get('generate', '');
        if($generate == "tags") {
            $showid = @$_REQUEST['showid'] or die("Missing show id variable");
            $show = ShowModel::FromId($showid);
            if(!$show) die("Error: \$show is empty, which means the show you are looking for does not exist, or an invalid showid was set as request variable.");

            // display current tags
            $html = "<h2>My Tags</h2><table class='formtable' cellspacing='0' cellpadding='0' style='width:360px;margin:10px auto;'>
		<!-- tr style='background:rgba(0,0,0,.2);'><th>Name</th><th>Listen</th><th>Delete</th></tr -->";
            $result = DB::GetAll("SELECT * FROM tags WHERE showid=\"$showid\"");
            $total = 0;
            foreach($result as $row){
                $rowclass = ++$total % 2 == 0 ? 'evenRow' : 'oddRow';
                $html .= "<tr class='$rowclass'>
			<td>$row[label]</td>
			<td>".miniPlayer($row['url'])."</td>
			<td><form method='post' onsubmit='return confirm(\"Are you absolute sure? You are about to permanently delete this tag, forever.\");'>
				<input type='hidden' name='tagid' value='$row[tagid]' />
				<input type='hidden' name='showid' value='$showid' />
				<input type='submit' name='DELETE_TAG' value=' Delete ' style='width:auto;' onclick='return confirm(\"Are you sure you want to delete this tag?\");' />
			</form></td>
		</tr>";
            }
            if(!$total) {
                $html .= "<tr class='oddRow'><td style='text-align:center;font-style:italic;color:#757575;'>No Tags Found. <br />You should upload one!</td></tr>";
            }
            $html .= "</table>";
            // upload a new tag
            $html .= "<h2>Upload a New Tag</h2><p>Need help? Check out our <a href='/dj/tags'>resources for creating show tags</a>.</p>";
            $html .= "<form method='post' enctype='multipart/form-data'><table class='formtable' cellspacing='0' cellpadding='0' style='width:360px;'>
		<tr class='oddRow'>
			<td>Label</td>
			<td><input type='text' name='label' value=\"".htmlentities($show->name)." Show Tag\" /></td>
		</tr>
		<tr class='evenRow'>
			<td>Upload<br /><span style='font-size:12px;color:#848484;font-style:italic;'>.mp3 files only, 3MB max</span></td>
			<td><input type='file' name='file' /></td>
		</tr>
		<tr class='oddRow'>
			<td colspan='2' style='text-align:center;'>
				<input type='hidden' name='showid' value='$showid' />
				<input type='hidden' name='UPLOAD_TAG' value='true' />
				<input type='submit' value=' Upload Tag ' onclick='this.value=\"Uploading...\";this.disabled=true;' />
			</td>
		</tr>
	</table></form>";
            // output
            print $html;
            exit;
        }
        else if($generate == 'stats') {
            $showid = @$_REQUEST['showid'] or die("Missing show id variable");
            // collect detailed stats information
            $stats = Stats::show($showid);
            foreach($stats['stats'] as $i => $dat)
                $stats['stats'][$i]['label'] = str_replace("- ", "<br /><b>", $stats['stats'][$i]['label']) . "</b>";
            print json_encode( $stats );
            exit;
        }
        else if($generate == 'recordings') {
            // get the showid
            $showid = @$_REQUEST['showid'] or die("Missing show id variable");
            if(!is_numeric($showid)) die("the showid that you entered is not numeric");
            // prepare html
            $html = "<h2>Recordings</h2>";
            $html .= "<p>Edit Info and view Stats.</p>";
            $html .= "<div class='recording_data'>";
            // get the mp3s
            $result = DB::GetAll("SELECT * FROM mp3s WHERE showid=$showid ORDER BY recordedon DESC");
            if(empty($result)) $html .= "No Recordings";
            else $html .= "<div class='thead'>
			<div class='col th1'>Date</div>
			<div class='col th2'>Name</div>
			<div class='col th3'><img src='/img/icons/downloads.png' alt='' title='Downloads' /></div>
			<div class='col th4'><img src='/img/icons/streams.png' alt='' title='Streams' /></div>
			<div class='col th5'><img src='/img/icons/podcasts.png' alt='' title='Podcasts' /></div>
		</div><div class='tbody'>";

            $total = 0;
            foreach($result as $row){
                $rowclass = ++$total % 2 == 0 ? 'evenRow' : 'oddRow';
                $date = date("m/d/y", strtotime($row['recordedon']) );
                $name = $row['label'] ? $row['label'] : "<i>- No Name -</i>";
                $activechecked = $row['active'] ? "checked='checked'" : "";
                $html .= "<a class='row $rowclass' id='recording_data-show$showid-row$total' rel=''>
			<span class='col col1'>$date</span>
			<span class='col col2'>$name</span>
			<span class='col col3'>$row[downloads]</span>
			<span class='col col4'>$row[streams]</span>
			<span class='col col5'>$row[podcasts]</span>
		</a>
		<div class='data $rowclass' id='recording_data-show$showid-row$total-data'>
			<form method='post' action=''>
				<input type='hidden' name='showid' value='$showid' />
				<input type='hidden' name='mp3id' value='$row[mp3id]' />
				<table cellspacing='0' cellpadding='0' class='formtable'>
				<tr class='oddRow'><td>Name</td><td><input type='text' name='label' value=\"".htmlentities($row['label'])."\" /></td></tr>
				<tr class='evenRow'><td colspan='2'>Description<br />
				<textarea name='description'>".htmlentities($row['description'])."</textarea></td></tr>
				<tr class='oddRow'><td colspan='2'><input type='checkbox' name='active' value='1' style='width:auto;' $activechecked /> Active <br /><small>If Active, this mp3 will be available for download, streaming, or podcasting.</small></td></tr>
				<tr class='evenRow'><td colspan=2'><input type='submit' name='updatemp3' value=' Save ' /></td></tr>
				</table>
			</form>
		</div>
		";
            }

            $html .= "</div></div>";
            $html .= "<script type='text/javascript'>setupRecordings()</script>";

            print $html;
            exit;
        }
        else if($generate == 'preferences') {
            // get showid
            $showid = @$_REQUEST['showid'] or die("Missing show id request variable.");
            $html = "<h2>Show Preferences</h2>";
            // prepare table
            $html .= "<form method='post'><input type='hidden' name='showid' value='$showid' />";
            $html .= "<table class='formtable' cellspacing='0' cellpadding='0' style='width:360px;margin:10px auto;'>";
            $fields = array("showname"=>"Show Name","genre"=>"Genre","description"=>"Description","link"=>"Website Link<br /><span style='color:#848484;font-size:12px'>(optional)</span>","explicit"=>"Explicit","podcastcategory"=>"Podcast Category");
            $total = 0;
            // get and output data
            $row = DB::GetFirst("SELECT * FROM shows WHERE showid='$showid'");
            foreach($fields as $field => $eng) {
                $rowclass = (++$total % 2 == 0) ? 'evenRow' : 'oddRow';
                switch($field) {
                    case 'description':
                        $html .= "<tr class='$rowclass'><td colspan='2' style='text-align:center;'>$eng<br /><textarea name='$field'>$row[$field]</textarea></td></tr>";
                        break;
                    case 'explicit':
                        if($row[$field]) $html .= "<tr class='$rowclass'><td>$eng<br /><img src='/img/misc/explicit.gif' alt='' /></td><td>
					<input type='radio' name='$field' value='0' style='width:auto;' /> Clean<br />
					<input type='radio' name='$field' value='1' style='width:auto;' checked='checked' /> Expicit</td></tr>";
                        else $html .= "<tr class='$rowclass'><td>$eng</td><td>
					<input type='radio' name='$field' value='0' style='width:auto;' checked='checked' /> Clean<br />
					<input type='radio' name='$field' value='1' style='width:auto;' /> Expicit</td></tr>";
                        break;
                    case 'podcastcategory':
                        $select = "";
                        $podcastcategories = Podcast::$categories;
                        foreach($podcastcategories as $optgroup => $categories) {
                            $select .= "<optgroup label='$optgroup'>";
                            foreach($categories as $val => $option) {
                                $selected = trim($val) == trim($row[$field]) ? "selected='selected'" : "";
                                $val = htmlentities($val);
                                $select .= "<option value='$val' $selected>$option</option>";
                            }
                            $select .= "</optgroup>";
                        }
                        $html .= "<tr class='$rowclass'><td>$eng</td><td><select name='$field'>$select</select></td></tr>";
                        break;
                    case 'genre':
                        $options = Site::$Genres;
                        $select = "";
                        foreach($options as $option) {
                            $option = trim($option);
                            $selected = ($option == trim($row[$field])) ? "selected='selected'" : "";
                            $select .= "<option value='$option' $selected>$option</option>";
                        }
                        $html .= "<tr class='$rowclass'><td>$eng</td><td><select name='$field'>$select</select></td></tr>";
                        break;
                    default:
                        $html .= "<tr class='$rowclass'><td>$eng</td><td><input name='$field' value=\"".htmlentities($row[$field])."\" /></td></tr>";
                }
            }
            // finish up
            $rowclass = (++$total % 2 == 0) ? 'evenRow' : 'oddRow';
            $html .= "<tr class='$rowclass'><td colspan='2' style='text-align:center;'><input type='submit' name='savepreferences' value=' Save Preferences ' /></td></tr>";
            $html .= "</table>";
            print $html;
            exit;
        }

        Template::SetBodyHeading("Chapman Radio", "My Shows");
        if(Site::$Applications) {
            $season = Site::ApplicationSeason();
            $seasonName = Season::name($season);
            Template::AddBodyContent("<div class='gloss'><h3>Applications are Open</h3><p>Apply for a show for $seasonName</p><a href='/dj/apply' class='ui_button115' style='margin:auto;'>Apply</a></div>");
        }


// should we notify?
        if($notify) Template::AddBodyContent("<div class='specs' style='width:480px;margin:10px auto;text-align:center;'>$notify</div>");
// fetch array of shows for this DJ
        $shows = ShowModel::FromDj($user->getId());
        if(empty($shows))
            return Template::Finalize($this->container,"<div style='margin:20px 80px;padding:20px;background:#EFEFEF;text-align:center;color:#757575;'>Sorry, ". $user->getUsername().". It doesn't look like you have any shows on your account.<br /><br />Note: If you just submitted an application, it may take a few days for it to be finalized and appear here.</div>");
// now we have all the shows as an array. let's display them all!
        foreach($shows as $show) {

            if(!Site::$Applications && $show->status == 'incomplete') continue;

            $temp = DB::GetFirst("SELECT count(*) as count FROM mp3s WHERE showid= :id", array(":id" => $show->id));
            $recordings = $temp['count'];
            if(!$recordings) $recordings = "are <i>no recordings</i>";
            else if($recordings == 1) $recordings = "is <b>1 recording</b>";
            else $recordings = "are <b>$recordings recordings</b>";

            // collect general stats information
            $temp = DB::GetFirst("SELECT max(chapmanradio+chapmanradiolowquality) AS peak FROM stats WHERE showid='".$show->id."'");
            $peak = $temp['peak'];
            if(!$peak) $peakHTML = "<i>no data</i>";
            else if($peak == 1) $peakHTML = "<b>1 listener</b>";
            else $peakHTML = "<b>$peak listeners</b>";

            // output
            if($peak) $statsHTML = Stats::generateHTML($show->id);
            else $statsHTML = "<h2>Listenership Statistics</h2><br /><p>No Data.</p>";

            $status = "Unknown";
            switch($show->status){
                case 'incomplete':
                    if(Site::$Applications)
                        $status = "<span style='color:#A00;'><a href='/dj/apply'>Incomplete</a></span>";
                    else
                        $status = "<span style='color:#A00;'>Incomplete!</span>";
                    break;
                case 'finalized':
                    $status = "<span style='color:#00A;'>Submitted</span>";
                    break;
                case 'accepted':
                    $status = "<span style='color:#0A0;'>Approved and Scheduled</span>";
                    break;
            }

            Template::AddBodyContent("<table><tr><td style='vertical-align: top;'>
	
	<table class='formtable' cellspacing='0' cellpadding='0' style='margin:10px;text-align:left;'>
		<tr>
			<td colspan=3>
				<span style='font-size: 18px;'><a href='".$show->permalink."'>".$show->name."</a></span> <span style='color:#646464'>#".$show->id."</span>
			</td>
		</tr>
		<tr>
			<td rowspan=7>
				<a href='".$show->permalink."'><img src='".$show->img90."' /></a>
			</td>
			<td class='evenRow'>DJs</td><td class='evenRow'>".$show->GetDjNamesCsv()."</td>
		</tr>
		<tr class='oddRow'><td>Status</td><td>$status</td></tr>
		<tr class='evenRow'><td>Seasons</td><td>".$show->seasons_csv."</td></tr>
		<tr class='oddRow'><td>Genre</td><td>".$show->genre."</td></tr>
		<tr class='evenRow'><td>Description</td><td>".$show->description."</td></tr>
		<tr class='oddRow'><td>Recordings</td><td>There $recordings of ".$show->name."</td></tr>
		<tr class='evenRow'><td>Stats</td><td>All-time peak: $peakHTML</td></tr>
	</table>
	
	</td><td>
	
	<div class='showdata show".$show->id."data' id='show".$show->id."' style='margin:10px;padding:0;width:422px;'>
		<div class='tabs'><ul>
			<li><a data-showid='".$show->id."' data-pane='stats' data-toggle-target='show".$show->id."stats'>Stats</a></li>
			<li><a data-showid='".$show->id."' data-pane='recordings' data-toggle-target='show".$show->id."recordings'>Recordings</a></li>
			<li><a data-showid='".$show->id."' data-pane='preferences' data-toggle-target='show".$show->id."preferences'>Preferences</a></li>
			<li><a data-showid='".$show->id."' data-pane='tags' data-toggle-target='show".$show->id."tags'>Tags</a></li>
			<li><a data-showid='".$show->id."' data-pane='pic' data-toggle-target='show".$show->id."pic'>Picture</a></li>
		</ul></div>
		<div class='gloss' style='display:none;margin:0;' id='show".$show->id."stats'>$statsHTML</div>
		<div class='gloss' style='display:none;margin:0;' id='show".$show->id."recordings'></div>
		<div class='gloss' style='display:none;margin:0;' id='show".$show->id."preferences'></div>
		<div class='gloss' style='display:none;margin:0;' id='show".$show->id."tags'></div>
		
		<div class='gloss' style='display:none;margin:0;' id='show".$show->id."pic'>
			<h2>Show Picture</h2>
			<table class='formtable' cellspacing='0' cellpadding='0' style='width:360px;margin:10px auto;text-align:center;'>
			<tr class='evenRow'><td colspan='2'><img id='dj-show-".$show->id."-picture' src='".$show->img310."' style='max-width: 310px;' alt='' /></td></tr>
			<tr class='oddRow'><td style='width:300px;text-align:left;' colspan='2'>".Uploader::RenderModel($show, 'dj-show-'.$show->id.'-picture')."</td></tr>
			</table>
		</div>
	</div>
	
	</td></tr></table>");
        }
        return Template::Finalize($this->container);

    }
}