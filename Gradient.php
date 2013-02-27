<?php
/**
 * Create gradient image using imagick
 * @author Lionel Chan <chaoszcat@gmail.com>. All rights reserved.
 * 
 * Requires Imagick [http://www.php.net/manual/en/class.imagick.php]
 * 
 * Usage:
 * $gradient = new Gradient();
 * $gradient->draw();
 * 
 * To customize it:
 * $gradient = new Gradient();
 * $gradient->gradient('#219801')
 *        .... (refering to the properties)
 *        ->draw();
 * 
 * To draw it into a file instead
 * $gradient->draw('path/to/the/file');
 */
class Gradient {
	
	/**
	 * Default gradient properties
	 * @var array
	 */
	private $properties = array(
		'width' => 1,
		'height' => 40,
		'color' => '#ff6600',   //Gradient Color
		'glossy' => true,
		
		/**
		 * Image type. GIF genenerally produces smaller file.
		 * Supported gif/png.
		 */
		'imageType' => 'gif',
		
		/**
		 * Unless specified, these values will be automatically
		 * calculated based on color
		 */
		'startColor' => null,
		'endColor' => null
	);
	
	/**
	 * We don't want someone to mess with the system so
	 * set a maximum dimension
	 */
	private $maxWidth = 8;
	private $maxHeight = 250;
	
	public function __construct() {
		$this->here = dirname(__FILE__);
	}
	
	private function setProp($k, $v) {
		if (isset($this->properties[$k])) {
			
			//Normalize colors
			if (in_array($k, array('color'))) {
				if ($v[0] != '#') {
					$v = '#'.$v;
				}
			}
			
			$this->properties[$k] = $v;
		}
	}
	
	private function getProp($k) {
		return isset($this->properties[$k]) ? $this->properties[$k] : null;
	}
	
	/**
	 * For get/set properties
	 * 
	 * @param type $name
	 * @param type $arguments
	 * @return null|Gradient
	 */
	public function __call($name, $arguments) {
		if (empty($arguments)) {
			return $this->getProp($name);
		}else{
			//attempt to set prop
			$this->setProp($name, $arguments[0]);
			return $this;
		}
	}
	
	/**
	 * Do background gradient
	 */
	private function doGradient() {
		$pattern = new Imagick();
		
		if ($this->startColor() && $this->endColor()) {
			$startColor = $this->startColor();
			$endColor = $this->endColor();
		}else{
			$startColor = $this->color();
			$endColor = $this->darken($startColor, 50);
		}
		
		$pattern->newpseudoimage(2, $this->height(), "gradient:{$startColor}-{$endColor}");

		//Gradient as pattern
		$background = new ImagickDraw();
		$background->pushPattern('gradient', 0, 0, 2, $this->height());
		$background->composite(Imagick::COMPOSITE_OVER, 0, 0, 2, $this->height(), $pattern);
		$background->popPattern();

		/* Set the gradient color.
		Changing this value changes the color of the gradient */
		$background->setFillPatternURL('#gradient');
		//Weird here. Need to reduce 1px down for some strange reason.
		$background->rectangle(0, 0, $this->width(), $this->height());
		
		$this->final->drawImage($background);
		$background->destroy();
	}
	
	/**
	 * Do the glossy effect
	 */
	private function doGlossy() {
		
		if (!$this->glossy()) return;
		
		$shine = new ImagickDraw();
		$shine->setFillColor("white");
		$shine->setFillAlpha(0.2);
		$shine->rectangle(0,0,$this->width(),$this->height()/2);
		$this->final->drawImage($shine);
		$shine->destroy();
	}
	
	/**
	 * Helper to convert hex string to rgb
	 * @param string $hex
	 * @return array
	 */
	private function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb; // returns an array with the rgb values
	}
	
	/**
	 * Helper to convert rgb array to hex string
	 * @param array $rgb
	 * @return string
	 */
	private function rgb2hex($rgb) {
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
		return $hex;
	}
	
	/**
	 * Darken a given color by $steps number
	 * @param type $color
	 * @return hex string
	 */
	private function darken($color, $steps=10) {
		//Convert string color #xxxxxx to rgb first
		if (is_string($color)) $color = $this->hex2rgb($color);
		for ($i = 0 ; $i < 3 ; $i++) {
			$color[$i] = $color[$i] - $steps;
			if ($color[$i] < 0) $color[$i] = 0;
		}
		return $this->rgb2hex($color);
	}
	
	/**
	 * Read the $_GET parameters of the whitelisted fields
	 * @param array $whitelist a list of whitelisted properties
	 */
	public function readGET($whitelist=array()) {
		
		if (is_string($whitelist)) {
			$whitelist = str_replace(' ', '', $whitelist);
			$whitelisted_properties = explode(',', $whitelist);
		}else{
			$whitelisted_properties = $whitelist;
		}
		
		foreach($whitelisted_properties as $k) {
			if (isset($_GET[$k])) {
				$this->setProp($k, $_GET[$k]);
			}
		}
		return $this;
	}
	
	/**
	 * Draw the gradient on screen, or into a file specified
	 */
	public function draw($file=null) {
		
		//Start the fun!
		$this->final = new Imagick();
		
		//We want to block gradient which is ridiculously big
		if ($this->width() > $this->maxWidth) {
			$this->width($this->maxWidth);
		}
		
		if ($this->height() > $this->maxHeight) {
			$this->height($this->maxHeight);
		}
		
		$this->final->newImage($this->width(), $this->height(), '#ffffff', $this->imageType());
		
		$this->doGradient();
		$this->doGlossy();
		
		
		if (!empty($file)) {
			$this->final->writeImage($file);
		}else{
			//echo
			$mime = $this->imageType();
			header("Content-Type: image/{$mime}");
			echo $this->final;
		}
		
		//Cleanup
		$this->final->destroy();
		
		return true;
	}
}