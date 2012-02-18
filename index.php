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
require 'errorHandling.php';
require 'locoImage.php';
require 'class.Template.php';

$numberOfExercises=12;
$resultSize=28; // multiple of 4


$layout=getLayout();

//------------------------------------------------------------------------------
// Load Template
$template = \Potherca\Template::fromFile('index.template.html');
// Build Relevant Content Blocks

function fetchAssignmentArray($numberOfExercises)
{ //fill array with assignments
    $assignmentTemp = $_POST['assignment'];
    $outcomeTemp = $_POST['outcome'];

    $assignment = array();
    for ($i = 1; $i <= $numberOfExercises; $i++) {
        $tempAssignment = $assignmentTemp[$i];
        $tempOutcome = $outcomeTemp[$i];
        //@FIXME: This creates a bug where, if two or more assignment have the same value, we overwrite $assignmentKeys and reduce the actual amount of keys available later on (also messing up the order.
        $assignment[$tempAssignment] = $tempOutcome;
    }
    return $assignment; #for
}

if (empty($_POST['assignment'])){
    //no input so display form

    // Remove Views we don't want
    $template->getBody()->removeChild(
        $template->getElementsByClassName('result-view')->item(0)
    );

    // Fetch nodes we'll work with
    $resultTable = $template->getElementsByClassName('result-preview')->item(0);
    $assignmentTable = $template->getElementsByClassName('assignment')->item(0);

    // Build preview
    buildResultPreview($layout, $resultSize, $resultTable, $template);

    // Add Assignment Form to Template
    buildAssignmentContent($numberOfExercises, $template, $assignmentTable);
} else {
    //input so display result

    // Remove Views we don't want
    $template->getBody()->removeChild(
        $template->getElementsByClassName('form-view')->item(0)
    );

    // Fetch nodes we'll work with
    $assignmentResultTable = $template->getElementsByClassName('assignment-result')->item(0);
    $resultTable = $template->getElementsByClassName('images-result')->item(0);
    $answersResultTable = $template->getElementsByClassName('answers-result')->item(0);


    $assignment = fetchAssignmentArray($numberOfExercises);
    $assignmentKeys = array_keys($assignment);

    // Add Assignment Result to Template
    buildAssignmentResult($assignmentResultTable, $template, $layout, $assignmentKeys);

    // Build result
    buildResultPreview($layout, $resultSize, $resultTable, $template);

    //show the answers
    buildResultContent($answersResultTable, $template, $layout, $assignmentKeys, $assignment);
}#if

// Output Template
echo $template;
//------------------------------------------------------------------------------



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

function buildResultPreview($layout, $resultSize, DOMElement $tableNode, \Potherca\Template $template) {

    $allSlices = buildSlicesForLayout($layout);

    addTilesToTableNode($allSlices, $resultSize, $tableNode, $template);
}

function addTilesToTableNode($allSlices, $resultSize, DOMElement $tableNode, \Potherca\Template $template)
{
    $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
    $template->removeChildrenFromNode($tableBodyNode);


    foreach ($allSlices as  $key => $value) {
        if ($key % 6 === 0) {
            // first cell of this row of 6
            $currentRow = $template->createElement('tr');
            $tableBodyNode->appendChild($currentRow);
        }#if

        createTile($resultSize, $value);

        $tableCell = $template->createElement('td');
        $tableCell->setAttribute('class', 'result');

        $image = $template->createElement('img');
        $image->setAttribute('src', 'images/'.$value.'.png');

        /** @noinspection PhpUndefinedVariableInspection */
        $currentRow->appendChild($tableCell);
        $tableCell->appendChild($image);
    }#foreach
}

function buildSlicesForLayout($layout)
{
    $layoutFlip = array_flip($layout);
    ksort($layoutFlip);
    //flip rows..
    $upperRow = array_slice($layoutFlip, 6, 6);
    $lowerRow = array_slice($layoutFlip, 0, 6);
    $allSlices = array_merge($upperRow, $lowerRow);
    return $allSlices;
}

function buildAssignmentContent($numberOfExercises, \Potherca\Template $template, \DOMElement $assignmentTable)
{
    $assignmentTableBody = $assignmentTable->getElementsByTagName('tbody')->item(0);
    $template->removeChildrenFromNode($assignmentTableBody);

    for ($i = 1; $i <= $numberOfExercises; $i++) {
        // Add row to table
        $currentRow = $template->createElement('tr');
        $assignmentTableBody->appendChild($currentRow);

        // Number
        $numberCell = $template->createElement('td', $i);

        // Assignment
        $assignmentCell = $template->createElement('td');
        $assignmentInputNode = $template->createElement('input');
        $assignmentInputNode->setAttribute('type', 'text');
        $assignmentInputNode->setAttribute('name', 'assignment[' . $i . ']');
        $assignmentCell->appendChild($assignmentInputNode);

        // Outcome
        $outcomeCell = $template->createElement('td');
        $outcomeInputNode = $template->createElement('input');
        $outcomeInputNode->setAttribute('type', 'text');
        $outcomeInputNode->setAttribute('name', 'outcome[' . $i . ']');
        $outcomeCell->appendChild($outcomeInputNode);

        // Add everything to the row
        $currentRow->appendChild($numberCell);
        $currentRow->appendChild($assignmentCell);
        $currentRow->appendChild($outcomeCell);
    }
    #for
}



function buildResultContent(DOMElement $tableNode, \Potherca\Template $template, $layout, $assignmentKeys, $assignment)
{
    $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
    $template->removeChildrenFromNode($tableBodyNode);

    $layoutFlip = array_flip($layout);
    ksort($layoutFlip);
    $count = 0;
    foreach ($layoutFlip as $key => $value) {
        $value--;
        $assignmentKey = $assignmentKeys[$value];
        if ($count % 6 === 0) {
            $currentRow = $template->createElement('tr');
            $tableBodyNode->appendChild($currentRow);
        }

        $tableCell = $template->createElement('td', $assignment[$assignmentKey]);
        $tableCell->setAttribute('class', 'answer');

        /** @noinspection PhpUndefinedVariableInspection */
        $currentRow->appendChild($tableCell);

        $count++;
    }
    #foreach
}

function buildAssignmentResult(DOMElement $tableNode, \Potherca\Template $template, $layout, $assignmentKeys)
{
    $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
    $template->removeChildrenFromNode($tableBodyNode);

    foreach ($layout as $key => $value) {
        if (($key - 1) % 6 === 0) {
            $currentRow = $template->createElement('tr');
            $tableBodyNode->appendChild($currentRow);
        }#if

        $tableCell = $template->createElement('td');
        $tableCell->setAttribute('class', 'assignment');

        $font = $template->createElement('span', $key . ')');
        $font->setAttribute('class', 'number');

        //        $content .= "<br><br>";
        $center = $template->createElement('center', $assignmentKeys[$key - 1]);
        //        $content .= "<br><br>";

        $tableCell->appendChild($font);
        $tableCell->appendChild($center);

        /** @noinspection PhpUndefinedVariableInspection */
        $currentRow->appendChild($tableCell);
    }#foreach
}


#EOF