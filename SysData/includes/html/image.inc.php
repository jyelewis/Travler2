<?php
if (isset($_GET['imageresize']))
{
	$imageurl = base64_decode($_GET['imageresize']);
	$imageurl = ereg_replace("[^0-9]", "", $imageurl);
	header('Content-type: image/jpeg');
	readfile($tmpdir.$imageurl.'.image');
	exit();
}
//start class ---------------------------------------------------------------------------------
class html_image extends html_attributes
{
	protected $content;
	protected $attributes;
	
	public function __construct($src)
	{
		$this->src = $src;
		$this->classes = array();
		$this->attributes = array('alt' => '');
		$this->resize = FALSE;
	}
	
	public function getsource()
	{
		global $tmpdir, $objectdir;
		if ($this->resize)
		{
			$tmpimagenum = rand()+rand();
			$tmpimagename = $tmpimagenum.'.image';
			if ($this->resizewidth == 0 || $this->resizeheight == 0){ $ispor = TRUE; } else { $ispor = FALSE; }
			file_put_contents($tmpdir.$tmpimagename, file_get_contents($this->src));
			smart_resize_image($tmpdir.$tmpimagename, $this->resizewidth, $this->resizeheight, $ispor, 'file');
			$this->src = $objectdir.'?imageresize='.base64_encode($tmpimagenum);
		}
		$content = '<img src="'.$this->src.'"'.$this->getattributesource().' />';
		return $content.PHP_EOL;
	}
	
	public function src($src)
	{
		$this->src = $src;
	}
	
	public function width($width)
	{
		parent::addattribute('width', $width);
	}
	
	public function height($height)
	{
		parent::addattribute('height', $height);
	}
	
	public function alt($alt)
	{
		parent::addattribute('alt', $alt);
	}
	
	public function __tostring()
	{
		return $this->getsource();
	}
	
	public function resize($isresize = TRUE, $width = 0, $height = 0)
	{
		$this->resize = $isresize;
		if ($width == 0)
		{
			if (!isset($this->resizewidth)){ $onresizewidth = 0; } else { $onresizewidth = $this->resizewidth; }
		} else {
			$onresizewidth = $width;
		}
		if ($height == 0)
		{
			if (!isset($this->resizeheight)){ $onresizeheight = 0; } else { $onresizeheight = $this->resizeheight; }
		} else {
			$onresizeheight = $height;
		}
		$this->resizewidth = $onresizewidth;
		$this->resizeheight = $onresizeheight;
	}
	
	public function resizewidth($width)
	{
		$this->resizewidth = $width;
		$this->resize();
	}
	
	public function resizeheight($height)
	{
		$this->resizeheight = $height;
		$this->resize();
	}
	
	protected function enableresize()
	{
		if (!$this->resize)
		{
		if (!isset($this->resizewidth)){ $onresizewidth = 0; } else { $onresizewidth = $this->resizewidth; }
		if (!isset($this->resizeheight)){ $onresizeheight = 0; } else { $onresizeheight = $this->resizeheight; }
		$this->resize(TRUE, $onresizewidth, $onresizeheight);
		}
	}
	
	public static function resized($image, $resizewidth, $resizeheight)
	{
                global $tempdir;
		$tmpimagenum = rand()+rand();
		$tmpimagename = $tmpimagenum.'.image';
		if ($resizewidth == 0 || $resizeheight == 0){ $ispor = TRUE; } else { $ispor = FALSE; }
                file_put_contents($tempdir.$tmpimagename, file_get_contents($image));
		smart_resize_image($tempdir.$tmpimagename, $resizewidth, $resizeheight, $ispor, 'file');
		$imagelocation = $tempdir.$tmpimagename;
		$imagedata = file_get_contents($imagelocation);
		unlink($imagelocation);
                return $imagedata;
	}
}
?>
<?php
//image resize function here - ------------------------------------------------------------------------ ignore and no not change
function smart_resize_image($file,
                              $width              = 0, 
                              $height             = 0, 
                              $proportional       = true, 
                              $output             = 'file', 
                              $delete_original    = FALSE, 
                              $use_linux_commands = false ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;

    # Setting defaults and meta
    $info                         = getimagesize($file);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;

    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );

      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }

    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
      case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
      case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);

      if ($transparency >= 0) {
        $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
        $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
    
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }

    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
      case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
      default: return false;
    }

    return true;
  }
?>
