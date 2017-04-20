<?php namespace ChapmanRadio;

class Waveform {
	public function generate($filename, $outputfile, $width=840, $height=120) {
		$foreground = "#0C263F"; // dark blue
		$foreground = "#92ABB7"; // light blue
		$foreground = "#A6CF2D"; // green
		$background = "#FFFFFF";
		
		if(!file_exists($filename)) die("waveform generate failed: $filename does not exist");
		
		/**
		 * Below as posted by "zvoneM" on
		 * http://forums.devshed.com/php-development-5/reading-16-bit-wav-file-318740.html
		 * as nadjiVrijednosti() defined below
		 */
		$handle = fopen ($filename, "r");
		//dohvacanje zaglavlja wav datoteke
		$zaglavlje[] = fread ($handle, 4);
		$zaglavlje[] = bin2hex(fread ($handle, 4));
		$zaglavlje[] = fread ($handle, 4);
		$zaglavlje[] = fread ($handle, 4);
		$zaglavlje[] = bin2hex(fread ($handle, 4));
		$zaglavlje[] = bin2hex(fread ($handle, 2));
		$zaglavlje[] = bin2hex(fread ($handle, 2));
		$zaglavlje[] = bin2hex(fread ($handle, 4));
		$zaglavlje[] = bin2hex(fread ($handle, 4));
		$zaglavlje[] = bin2hex(fread ($handle, 2));
		$zaglavlje[] = bin2hex(fread ($handle, 2));
		$zaglavlje[] = fread ($handle, 4);
		$zaglavlje[] = bin2hex(fread ($handle, 4));
		
		//bitrate wav datoteke
		$peek = hexdec(substr($zaglavlje[10], 0, 2));
		$bajta = $peek / 8;
		
		//provjera da li se radi o mono ili stereo wavu
		$kanala = hexdec(substr($zaglavlje[6], 0, 2));
		
		if($kanala == 2){
			$omjer = 40;
		}
		else{
			$omjer = 80;
		}

		while(!feof($handle)){
			$bytes = array();
			//get number of bytes depending on bitrate
			for ($i = 0; $i < $bajta; $i++){
				$bytes[$i] = fgetc($handle);
			}
			switch($bajta){
					//get value for 8-bit wav
				case 1:
					$data[] = Waveform::nadjiVrijednosti($bytes[0], $bytes[1]);
					break;
					//get value for 16-bit wav
				case 2:
					if(ord($bytes[1]) & 128){
						$temp = 0;
					}
					else{
						$temp = 128;
					}
					$temp = chr((ord($bytes[1]) & 127) + $temp);
					$data[]= floor(Waveform::nadjiVrijednosti($bytes[0], $temp) / 256);
					break;
			}
			//skip bytes for memory optimization
			fread ($handle, $omjer);
		}
		
		// close and cleanup
		fclose ($handle);
		
		/**
		 * Image generation
		 */
					
		// how much detail we want. Larger number means less detail
		// (basically, how many bytes/frames to skip processing)
		// the lower the number means longer processing time
		define("DETAIL", 5);
					
		// create original image width based on amount of detail
		$img = imagecreatetruecolor(sizeof($data) / DETAIL, $height);
		
		// fill background of image
		list($r, $g, $b) = Waveform::html2rgb($background);
		$bgcolor = imagecolorallocate($img, $r, $g, $b);
		imagefilledrectangle($img, 0, 0, sizeof($data) / DETAIL, $height, $bgcolor);
		
		// generate background color
		list($r, $g, $b) = Waveform::html2rgb($foreground);


		// loop through frames/bytes of wav data as genearted above
		$color = imagecolorallocate($img, $r, $g, $b);
		//imagecolortransparent($img, $color);
		for($d = 0; $d < sizeof($data); $d += DETAIL) {
			// relative value based on height of image being generated
			// data values can range between 0 and 255
			$v = (int) ($data[$d] / 255 * $height);
			// draw the line on the image using the $v value and centering it vertically on the canvas
			imageline($img, $d / DETAIL, 0 + ($height - $v), $d / DETAIL, $height - ($height - $v), $color);
		}
		
		
		// want it resized?
		if ($width) {
			
			// resample the image to the proportions defined in the form
			$rimg = imagecreatetruecolor($width, $height);
			imageantialias($rimg, true);
			imagecopyresampled($rimg, $img, 0, 0, 0, 0, $width, $height, sizeof($data) / DETAIL, $height);
			
			/*for($row = 0;$row < $width;$row++) {
				for($col = 0;$col < $height;$col++) {
					$rgb = @imagecolorat($rimg, $row, $col) or 0;
					if(!$rgb) { continue; }
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					if($r + $g + $b > 382) imagesetpixel($rimg, $row, $col, $bgcolor);
					else imagesetpixel($rimg, $row, $col, $color);
				}
			}*/
			
			//imagealphablending( $rimg, false );
			//imagesavealpha( $rimg, true );
			//list($r, $g, $b) = Waveform::html2rgb($foreground);
			//$color = imagecolorallocate($rimg, $r, $g, $b);
			//imagecolortransparent($rimg, $bgcolor);
			
			
			
			imagepng($rimg, $outputfile.".png");
			imagegif($rimg, $outputfile.".gif");
			imagedestroy($rimg);
			
		} else {
			
			// print out at it's raw width (size of $data / detail level)
			imagepng($img,$outputfile.".png");
			imagedestroy($img);
		
		}
		
	}
	
	private function nadjiVrijednosti($byte1, $byte2){
		$byte1 = hexdec(bin2hex($byte1));                        
		$byte2 = hexdec(bin2hex($byte2));                        
		return ($byte1 + ($byte2*256));
		}
	
	private function html2rgb($input) {
		$input=($input[0]=="#")?substr($input, 1,6):substr($input, 0,6);
		return array(
			hexdec( substr($input, 0, 2) ),
			hexdec( substr($input, 2, 2) ),
			hexdec( substr($input, 4, 2) )
			);
		}
	
	}