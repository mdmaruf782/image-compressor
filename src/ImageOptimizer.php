<?php
namespace MdMaruf\ImageOptimizer;
use File;
class ImageOptimizer 
{
	private static $path;
	private static $fullname;
	private static $fileUrl;
	private static $ext;

	public static function optimize($fileurl,$compress = 0)
	{
		
			
			$arr= explode('/', $fileurl);
			$path=str_replace(end($arr), '', $arr);
			$mainpath= implode("/", $path);

			ImageOptimizer::$path = $mainpath;
			ImageOptimizer::$fileUrl = $fileurl;
			ImageOptimizer::$fullname = end($arr);

			$ext=explode('.', $fileurl);

		

		if (file_exists($fileurl)) {
			return ImageOptimizer::compress($fileurl, end($ext), $compress);
		}
		
		 
	}





	private static function createOptimize($image, $name, $type, $size, $c_type, $level) {
		
		$im_output = ImageOptimizer::$path.ImageOptimizer::$fullname;
        $im_ex = explode('.', $im_output); // get file extension
        
        // create image
        if($type == 'image/jpeg' || $type == 'image/jpg'){
            $im = imagecreatefromjpeg($image); // create image from jpeg

        }else if($type == 'image/gif'){
            $im = imagecreatefromgif($image); // create image from gif
        }else{

            $im = imagecreatefrompng($image);  // create image from png (default)
        }

       File::delete(ImageOptimizer::$fileUrl);
        // compress image
        if(in_array($c_type, array('jpeg','jpg','JPG','JPEG'))){
            $im_name = str_replace(end($im_ex), 'jpg', ImageOptimizer::$fullname); // replace file extension
            $im_output = str_replace(end($im_ex), 'jpg', $im_output); // replace file extension
            if(!empty($level)){
                imagejpeg($im, $im_output, 100 - ($level * 10)); // if level = 2 then quality = 80%
            }else{
                imagejpeg($im, $im_output, 100); // default quality = 100% (no compression)
            }
            $im_type = 'image/jpeg';
        }else if(in_array($c_type, array('gif','GIF'))){
            $im_name = str_replace(end($im_ex), 'gif', ImageOptimizer::$fullname); // replace file extension
            $im_output = str_replace(end($im_ex), 'gif', $im_output); // replace file extension
            if(ImageOptimizer::check_transparent($im)) { // Check if image is transparent
            	imageAlphaBlending($im, true);
            	imageSaveAlpha($im, true);
            	imagegif($im, $im_output, 100 - ($level * 10));
            }
            else {
            	imagegif($im, $im_output, 100);
            }
            $im_type = 'image/gif';
        }else if(in_array($c_type, array('png','PNG'))){
            $im_name = str_replace(end($im_ex), 'png', ImageOptimizer::$fullname); // replace file extension
            $im_output = str_replace(end($im_ex), 'png', $im_output); // replace file extension
            if(ImageOptimizer::check_transparent($im)) { // Check if image is transparent
            	imageAlphaBlending($im, true);
            	imageSaveAlpha($im, true);
                imagepng($im, $im_output, $level); // if level = 2 like quality = 80%
            }
            else {
                imagepng($im, $im_output, $level); // default level = 0 (no compression)
            }
            $im_type = 'image/png';
        }
        
        // image destroy
        imagedestroy($im);
        
        // output original image & compressed image
        $im_size = filesize($im_output);
        $info = array(
        	'name' => $im_name,
        	'image' => $im_output,
        	'type' => $im_type,
        	'size' => $im_size 
        );
        return $info;
    }

    private static function check_transparent($im) {

        $width = imagesx($im); // Get the width of the image
        $height = imagesy($im); // Get the height of the image

        // We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
        for($i = 0; $i < $width; $i++) {
        	for($j = 0; $j < $height; $j++) {
        		$rgba = imagecolorat($im, $i, $j);
        		if(($rgba & 0x7F000000) >> 24) {
        			return true;
        		}
        	}
        }

        // If we dont find any pixel the function will return false.
        return false;
    }  
    
    private static function  compress($image, $c_type, $level = 0) {

        // get file info
    	$im_info = getImageSize($image);
    	$im_name = basename($image);
    	$im_type = $im_info['mime'];
    	$im_size = filesize($image);

        // result
    	$result = array();

        // cek & ricek
        if(in_array($c_type, array('jpeg','jpg','JPG','JPEG','gif','GIF','png','PNG'))) { // jpeg, png, gif only

        	$result['data'] = ImageOptimizer::createOptimize($image, $im_name, $im_type, $im_size, $c_type, $level);

        	return $result;
        }
    }
}