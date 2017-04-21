<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use function ChapmanRadio\error;
use ChapmanRadio\Season;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UploadController extends Controller
{
    /**
     * @Route("/staff/upload", name="staff_upload")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Upload - Site Administration");
        Template::SetBodyHeading("Site Administration", "File Upload");
        Template::RequireLogin("Staff Resources", "staff");

        $season = Season::Current();

// process upload
        if (isset($_POST['submit'])) {
            $path = @$_POST['path'] or error('path was not defined');
            $path = preg_replace("/^\\//", "", $path);
            $path = preg_replace("/\/\$/", "", $path);
            $base = getcwd();
            $base = preg_replace("/\/\$/", "", $base);
            $base = preg_replace("/^\\//", "", $base);
            $target = "/" . $base . "/" . $path . "/";
            $target = PATH . $path . "/";
            $name = $_FILES['file']['name'];
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $target . $name))
                Template::AddBodyContent("<p style='color:red;'>Upload to <tt>$target$name</tt> failed.</p>");
            else
                Template::AddBodyContent("<p style='color:green;'><a href='$target$name' target='_blank'><tt>$target$name</tt></a> Successfully Uploaded.</p>");
        }

// display page
        $downloads = self::makeForm("/downloads/$season/");
        if (!file_exists(PATH . "/downloads/$season/")) mkdir(PATH . "/downloads/$season");
        Template::AddBodyContent("
	<div class='leftcontent'>
	<p>Use this form to upload documents.<br />Anything uploaded here will be visible on <a href='/dj/downloads'>/dj/downloads</a>.</p>
	<div class='gloss' style='width:360px;margin:10px auto;text-align:center;'>$downloads</div>
	</div>
");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

    function makeForm($path, $changeable = false)
    {
        $title = ($changeable) ? "<input type='text' name='path' value='$path' />" : "$path<input type='hidden' name='path' value='$path' />";
        return "<form method='post' action='' enctype='multipart/form-data'>			
		<h3>Upload to <tt>$title</tt></h3>
		<p><input type='file' name='file' /></p>
		<p><input type='submit' name='submit' value=' Upload ' /></p>
		<br />
	</form>";
    }

}