<?php namespace ChapmanRadio;

class Uploader
{

    /* Renderers */

    public static function Render($identifier, $cropelement = NULL, $extra = '', $aspect = 1.0)
    {
        if (!isset($_REQUEST['nocropper'])) {
            Template::IncludeJs('/plugins/uploader/js/vendor/jquery.ui.widget.js');
            Template::IncludeJs('/plugins/uploader/js/jquery.iframe-transport.js');
            Template::IncludeJs('/plugins/uploader/js/jquery.fileupload.js');
            Template::IncludeJs('/plugins/jcrop/jquery.jcrop.min.js');
            Template::IncludeCss('/plugins/jcrop/jquery.jcrop.min.css');
            Template::IncludeJs('/js/uploader.js');
            Template::IncludeCss('/css/uploader.css');
        }
        return "
			<form method='post' action='$_SERVER[REQUEST_URI]' enctype='multipart/form-data' data-cropper='$cropelement' id='cr_uploader_" . $identifier . "_form' class='cr_uploader'>
				<div class='cr_uploader_upload_buttons'>
					" . $extra . "
					<input type='file' name='cr_uploader_" . $identifier . "_file' class='cr_uploader_file' id='cr_uploader_" . $identifier . "_file' />
					<input type='submit' name='cr_uploader_submit' value='Upload' />
				</div>
				<div class='cr_uploader_crop_buttons' style='display:none;'>
					<input type='hidden' name='cr_uploader_crop_aspect' value='{$aspect}' />
					<input type='hidden' name='cr_uploader_crop_x' value='' />
					<input type='hidden' name='cr_uploader_crop_y' value='' />
					<input type='hidden' name='cr_uploader_crop_h' value='' />
					<input type='hidden' name='cr_uploader_crop_w' value='' />
					<input type='hidden' class='cr_uploader_key' name='cr_uploader_key' value='' />
					<input type='submit' name='cr_uploader_savecrop' value='Crop' />
					<input type='submit' name='cr_uploader_cancelcrop' value='Skip this step' />
				</div>
			</form>";
    }

    public static function RenderModel($model, $cropelement = NULL)
    {
        $identifier = $model->objtype . "_" . $model->id;
        return Uploader::Render($identifier, $cropelement, "
			<input type='hidden' name='cr_uploader_modeltype' value='" . $model->objtype . "' />
			<input type='hidden' name='cr_uploader_modelid' value='" . $model->id . "' />",
            $model->ImageAspectRatio);
    }

    /* Handlers */

    public static function GetEmbeddedModel()
    {
        $modeltype = Request::Get('cr_uploader_modeltype');
        $modelid = Request::GetInteger('cr_uploader_modelid');
        if ($modeltype == '' || $modelid == 0) return NULL;
        switch ($modeltype) {
            case 'ShowModel':
                return ShowModel::FromId($modelid);
                break;
            case 'UserModel':
                return UserModel::FromId($modelid);
                break;
            case 'GiveawayModel':
                return GiveawayModel::FromId($modelid);
                break;
            case 'FeatureModel':
                return FeatureModel::FromId($modelid);
                break;
        }
        return NULL;
    }

    public static function HandleAnyModel()
    {
        $model = Uploader::GetEmbeddedModel();
        if ($model == NULL) return NULL;
        return Uploader::HandleModel($model);
    }

    public static function HandleModel($model)
    {
        if ($model == NULL || !isset($model->id)) return NULL;
        $identifier = $model->objtype . "_" . $model->id;
        if (!isset($_FILES) || !isset($_FILES['cr_uploader_' . $identifier . '_file'])) return NULL;
        $file = Uploader::Handle($identifier);
        return Uploader::PostProcessModelUpload($model, $file);
    }

    public static function Handle($identifier)
    {
        if (!isset($_FILES) || !isset($_FILES['cr_uploader_' . $identifier . '_file'])) return NULL;
        return Uploader::UploadFileByIdentifier('cr_uploader_' . $identifier . '_file');
    }

    /* Processors for uploaded file */

    /* Moves an uploaded file from CR temporary location to the model's content directory */
    /* Directs the model to handle the uploaded file to create any versions */
    public static function PostProcessModelUpload($model, $upload)
    {
        $uploadname = $model->imgbasepath . $model->imgpath . "upload-" . time() . ".jpg";
        if (!is_dir(PATH . $model->imgbasepath . $model->imgpath)) mkdir(PATH . $model->imgbasepath . $model->imgpath);

        rename(PATH . $upload, PATH . $uploadname);
        $model->HandleImage(PATH . $uploadname);
        return $uploadname;
    }

    /* Moves an uploaded file from system storage to CR temporary location */
    /* Converts image to Jpeg */
    public static function UploadFileByIdentifier($identifier)
    {
        $file = $_FILES[$identifier];

        if (!$file['name']) throw new \Exception("Error: No file was selected for upload");
        if ($file['size'] > 5000000) throw new \Exception("Error: File is too large"); // ~5mb

        $fileInfo = pathinfo($file['name']);

        if (!isset($fileInfo['extension'])) throw new \Exception("Error: Unrecognized file type");

        $file_id = Util::UniqueFileName();
        $file_path = "tmp/uploads/" . $file_id . "." . $fileInfo['extension'];

        if (!move_uploaded_file($file['tmp_name'], PATH . $file_path)) throw new \Exception("Error: Unable to save image");
        if (!Imaging::ImageToJpeg(PATH . $file_path)) throw new \Exception("Error: Not a valid image file");

        return "tmp/uploads/" . $file_id . ".jpg";
    }

}