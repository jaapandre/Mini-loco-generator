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
include ("locoImage.php");

$numberOfExercises=12;
$resultSize=28;//veelvoud van 4
//display form if no input
if (empty($_REQUEST['opgave'])){
?>
<table border=0 width=100%><tr><td width="80%">This is software as it is, distributed under the terms of the GNU GPL.<br>
If you want to contribute or have suggestions, please visit:<br>
<a href="https://github.com/jaapandre/Mini-loco-generator">https://github.com/jaapandre/Mini-loco-generator</a><br>
I hope it is helpfull, please let me know at <a href=mailto:j.dehoop@data-assist.nl>j.dehoop@data-assist.nl</a><br>
</td><td align=right>
<?php
$layout=getLayout();
showResultPreview($layout);
?>
</td></tr></table>
<hr>
<form method=post>
<table><tr>
<td></td><td>opgave</td><td>uitkomst</td>
</tr>
<?php
for ($i=1;$i<=$numberOfExercises;$i++) {
$str= <<<EOF
<tr>
<td>$i</td>
<td><input type="open" name="opgave[$i]"</td>
<td><input type="open" name="uitkomst[$i]"</td>
</tr>
EOF;
echo $str;
}
?>
</tr>
</table>
<input type="submit">
<?php

}else {
//fill array with opgaves
$opgaveTemp=$_REQUEST['opgave'];
$uitkomstTemp=$_REQUEST['uitkomst'];

for ($i=1;$i<=$numberOfExercises;$i++) {
	$tempOpgave=$opgaveTemp[$i];
	$tempUitkomst=$uitkomstTemp[$i];
	$opgave[$tempOpgave]=$tempUitkomst;
}


//show the page header
echo "<html><head>
<style >
td.opgave {
	width: 251px; 
	height:100px; 
}
td.antwoorden {
	width: 251px; 
	height:150px; 
	 text-align:center;
}
</style>
</head><body>\n";

$layout=getLayout();


//show the opgave
$opgaveKeys=array_keys($opgave);
$count=0;
echo "<table border=1><tr>\n";
foreach ($layout as $key=>$value) {
	$key--;
	$count++;
	if ($count==7){
		echo "</tr><tr>\n";
	}
	echo "<td class=\"opgave\">";
	echo "<font size=-1>$count)</font><br><br>";
	echo "<center>$opgaveKeys[$key]</center><br><br></td>\n";
}
echo "</tr></table>\n\n";
//echo "<br><br><br><br>";

//show result
showResultPreview($layout);


//show the answers 
$layoutFlip=array_flip($layout);
ksort($layoutFlip);
$count=0;
echo "<table border=1><tr>\n";
foreach ($layoutFlip as $key=>$value) {
	$value--;
	$opgaveKey=$opgaveKeys[$value];
	if (is_int($count/6)){
		echo "</tr><tr>\n";
	}
	echo "<td class=\"antwoorden\">$opgave[$opgaveKey]</td>\n";
	$count++;
}

//show page footer
echo "</tr></table>\n";
echo "</body></html>\n";
}

function getLayout ($id=null){
	//add here more layout, on which position should the tile come, first is the tile number, second one the position
	//add 24 layouts for loco with 24 tiles
	$layouts[]=array(1=>9, 2=>8,3=>11,4=>10,5=>5,6=>7,7=>4,8=>2,9=>12,10=>3,11=>6,12=>1);
	$layouts[]=array(1=>11, 2=>10,3=>7,4=>8,5=>1,6=>9,7=>2,8=>4,9=>12,10=>5,11=>6,12=>3);
	$layouts[]=array(1=>7, 2=>11,3=>9,4=>8,5=>4,6=>12,7=>1,8=>6,9=>10,10=>2,11=>3,12=>5);
	$layouts[]=array(1=>2, 2=>4,3=>11,4=>7,5=>5,6=>9,7=>1,8=>10,9=>6,10=>8,11=>12,12=>3);
	$layouts[]=array(1=>6, 2=>2,3=>3,4=>5,5=>9,6=>1,7=>11,8=>8,9=>4,10=>12,11=>10,12=>7);
	//$layouts[]=array(1=>, 2=>,3=>,4=>,5=>,6=>,7=>,8=>,9=>,10=>,11=>,12=>);
	
	//choose a layout
	
	shuffle($layouts);
	$layout=$layouts[0];
	return $layout;
}

function showResultPreview($layout) {
	global $resultSize;//veelvoud van 4
	$layoutFlip=array_flip($layout);
	ksort($layoutFlip);
	//flip rows..
	$upperRow=array_slice($layoutFlip,6,6);
	$lowerRow=array_slice($layoutFlip,0,6);
	$allSlices=array_merge($upperRow,$lowerRow);
	$count=0;
	echo "<table border=1><tr>\n";
	foreach ($allSlices	 as $key=>$value) {
		//	$value--;
		if (is_int($count/6)){
			echo "</tr><tr>\n";
		}
		createTile($resultSize,$value);
		echo "<td class=\"result\"><img src=$value.png></td>\n";
		$count++;
	}
	echo "</tr></table>\n";
} 


?>
