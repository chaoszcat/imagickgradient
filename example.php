<?php
include 'Gradient.php';

$gradient = new Gradient();

/**
 * Properties: width, height, color, glossy
 * Others: startColor, endColor. These properties synthesize itself from color
 */
$gradient->color('#ff3300')->draw();