Imagick Gradient
--------------

__Note__: (This is a fork from the imagickbutton, at [https://github.com/chaoszcat/imagickbutton](https://github.com/chaoszcat/imagickbutton))

A beautifully PHP rendered imagick gradient (1px width) for used in rendering button backgrounds

Requirements
------------

PHP 5.3 and [Imagick extension](http://php.net/manual/en/class.imagick.php) installed. Installation
steps is out of scope here. Google it, should have a lot of resources.

Example
------------

![ScreenShot](https://raw.github.com/chaoszcat/imagickgradient/master/example.gif)<br>

To draw it:

    $gradient = new Gradient();
    $gradient->width(8)
             ->draw();


Quick Start
-----------

- Get the `Gradient.php` class file
- Include the `Gradient.php` in your script

        include 'Gradient.php';
        $gradient = new Gradient();
        $gradient->draw();

- That's it!


Configurations
--------------

These are the default properties in the Gradient class.

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
		 * calculated based on buttonColor
		 */
		'startColor' => null,
		'endColor' => null
	);

To set it, simply call the respective properties as a method. For example, a blueish gradient:

    $gradient->gradientColor('#36a')
             ->draw();


A orange gradient (orangy color is the default color) with no glossy effect

    $gradient->glossy(false)
             ->draw();

To read $_GET automatically, use `readGET`. Pass an array of whitelisted $_GET keys in array, or CSV string

    $gradient->readGET(array('height', 'color'))
             ->draw();

To draw into a file instead, call this

    $gradient->draw('path/to/the/file');