<?php

/*

resize.php

*/

// will scale the image at $src down so that the max width or height doesn't exceed whatev
function smartResize($src, $resizeWidth, $resizeHeight) {
	list($width, $height) = getimagesize($src);
	
	$widthRatio = $resizeWidth/$width;
	$heightRatio = $resizeHeight/$height;
	
	if( $widthRatio < 1 || $heightRatio < 1) {
		if( $widthRatio < $heightRatio )
			$multFactor = $widthRatio;
		else
			$multFactor = $heightRatio;
		$toWidth = round($width * $multFactor);
		$toHeight = round($height * $multFactor);
		
		resize($src, $toWidth, $toHeight);
		
		return array($toWidth, $toHeight);
	}
	return array($width, $height);
}

// plain old resize function
function resize( $src, $width, $height) {
	$pic = new SimpleImage();
	$pic -> load($src);
	$pic -> resize($width, $height);
	$pic -> save($src);
//	ImageDestroy($pic -> $image);
}


class SimpleImage {
   var $image;
   var $image_type;
 
   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=92, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }      
}

?>