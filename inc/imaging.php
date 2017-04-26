<?php namespace ChapmanRadio;

require_once BASE."/lib/snoopy.php";

class Imaging
{

    public static function DownloadRemoteImage($url)
    {

        $file_path = "tmp/img-cache/" . sha1($url) . ".jpg";

        if (file_exists(PATH . $file_path)) return $file_path;

        file_put_contents(PATH . $file_path, file_get_contents($url));
        Imaging::ImageToJpeg(PATH . $file_path);

        return $file_path;
    }

    public static function BestCrop($src, $aspect)
    {
        $crops = array();
        list($width, $height) = getimagesize($src);
        if ($width == 0 || $height == 0) throw new \Exception("Invalid image file");

        // a = w/h    w = a * h    h = w / a

        $crops['x'] = 0;
        $crops['y'] = 0;
        $crops['w'] = $width;
        $crops['h'] = $height;

        if ($width / $height > $aspect) {
            // too wide - center horizontally at correct width
            $crops['w'] = ($aspect * $height); // width of target
            $crops['x'] = ($width - $crops['w']) / 2; // centered
        }

        if ($width / $height < $aspect) {
            // too tall - center vertically at correct height
            $crops['h'] = ($width / $aspect); // height of target
            $crops['y'] = ($height - $crops['h']) / 2; // centered
        }

        return $crops;
    }

    public static function ToImage($src)
    {
        list($w, $h, $t) = getimagesize($src);
        switch ($t) {
            case 1:
                return imagecreatefromgif($src);
            case 2:
                return imagecreatefromjpeg($src);
            case 3:
                return imagecreatefrompng($src);
            default:
                return false;
        }
    }

    public static function ImageToJpeg($src)
    {
        $f = pathinfo($src);
        list($w, $h, $t) = getimagesize($src);
        $d = Imaging::ToImage($src);
        if ($d == null) throw new \Exception("ImageToJpeg - no source");
        try {
            $n = imagecreatetruecolor($w, $h);
            imagefilledrectangle($n, 0, 0, $w, $h, imagecolorallocate($n, 255, 255, 255));
            imagecopyresampled($n, $d, 0, 0, 0, 0, $w, $h, $w, $h);
            unlink($src); // delete original
            imagejpeg($n, $f['dirname'] . '/' . $f['filename'] . '.jpg', 100); // save to same file, just different type
            unset($n); // free memory
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public static function CopyResizeCrop($src, $dest, $out_w, $out_h, $x, $y, $w, $h, $q = 90)
    {
        if (!file_exists($src)) return false;
        $img_r = imagecreatefromjpeg($src);
        $dst_r = ImageCreateTrueColor($out_w, $out_h);
        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $out_w, $out_h, $w, $h);
        imagejpeg($dst_r, $dest, $q);
        unset($img_r);
        unset($dst_r);
        return true;
    }

    public static function CopyResizeBestCrop($src, $dest, $out_w, $out_h, $q = 90)
    {
        $crops = Imaging::BestCrop($src, $out_w / $out_h);
        return Imaging::CopyResizeCrop($src, $dest, $out_w, $out_h, $crops['x'], $crops['y'], $crops['w'], $crops['h'], $q);
    }

}