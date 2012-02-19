<?php
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if(!is_writable('images/')){
    throw new UnexpectedValueException(
         'The images directory is not writable. '
        . 'This needs to be corrected before this script can run.'
    );
}

function createTile($size=200, $number=1) {
	$properties[1]=array('color'=>'green', 	'shape'=>array(0,0, 4,0, 4,4, 2,4));
	$properties[2]=array('color'=>'blue', 	'shape'=>array(0,0, 4,0, 4,4, 2,4));
	$properties[3]=array('color'=>'red', 	'shape'=>array(0,0, 4,0, 2,4, 0,4));
	$properties[4]=array('color'=>'green',	'shape'=>array(0,0, 4,0, 2,4, 0,4));
	$properties[5]=array('color'=>'red', 	'shape'=>array(0,0, 2,0, 4,4, 0,4));
	$properties[6]=array('color'=>'blue', 	'shape'=>array(0,0, 4,0, 2,4, 0,4));
	$properties[7]=array('color'=>'green', 	'shape'=>array(0,0, 2,0, 4,4, 0,4));
	$properties[8]=array('color'=>'blue', 	'shape'=>array(2,0, 4,0, 4,4, 0,4));
	$properties[9]=array('color'=>'red', 	'shape'=>array(0,0, 4,0, 4,4, 2,4));
	$properties[10]=array('color'=>'green', 'shape'=>array(2,0, 4,0, 4,4, 0,4));
	$properties[11]=array('color'=>'red', 	'shape'=>array(2,0, 4,0, 4,4, 0,4));
	$properties[12]=array('color'=>'blue', 	'shape'=>array(0,0, 2,0, 4,4, 0,4));


	$canvas = imagecreate( $size, $size );
	$white=imagecolorallocate($canvas, 255,255,255);
	imagefilledrectangle($canvas, 0, 0, $size, $size, $white);

	switch ($properties[$number]['color']){
	case 'blue':
		$color = imagecolorallocate( $canvas, 0, 0, 255 );
		break;
	case 'green':
		$color= imagecolorallocate( $canvas, 0, 255, 0 );
		break;
	case 'red':
		$color= imagecolorallocate( $canvas, 255, 0, 0 );
		break;
	}
	$values=$properties[$number]['shape'];
	$scale=$size/4;
	$i = 0;
	while (isset($values[$i])) {
	    $values[$i] *= $scale;
	    $i++;
	}

	imagefilledpolygon( $canvas, $values, 4,  $color );
	if (!imagepng($canvas, "images/$number.png")){
		echo "unable to create $number.png, size: $size, number: $number<br>";
	}
	imagedestroy($canvas);
}
