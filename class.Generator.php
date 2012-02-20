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

class Generator
{
////////////////////////////////// PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    protected $numberOfExercises=12;

    protected $resultSize=28; // multiple of 4

    protected $layouts = array(
        //add here more layout, on which position should the tile come, first is the tile number, second one the position
        //add 24 layouts for loco with 24 tiles
        1 => array(1=>9, 2=>8,3=>11,4=>10,5=>5,6=>7,7=>4,8=>2,9=>12,10=>3,11=>6,12=>1)
    , 2 => array(1=>11, 2=>10,3=>7,4=>8,5=>1,6=>9,7=>2,8=>4,9=>12,10=>5,11=>6,12=>3)
    , 3 => array(1=>7, 2=>11,3=>9,4=>8,5=>4,6=>12,7=>1,8=>6,9=>10,10=>2,11=>3,12=>5)
    , 4 => array(1=>2, 2=>4,3=>11,4=>7,5=>5,6=>9,7=>1,8=>10,9=>6,10=>8,11=>12,12=>3)
    , 5 => array(1=>6, 2=>2,3=>3,4=>5,5=>9,6=>1,7=>11,8=>8,9=>4,10=>12,11=>10,12=>7)
//        , array(1=>, 2=>,3=>,4=>,5=>,6=>,7=>,8=>,9=>,10=>,11=>,12=>);
    );

    protected $layout;

    protected $assignment;

////////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function getRandomLayout()
    {
        return $this->layouts[array_rand($this->layouts)];
    }

    public function getAssignment()
    {
        if(!isset($this->assignment)){
            $this->assignment = $this->fetchAssignmentArray();
        }#if

        return $this->assignment;
    }

    protected function getAssignmentKeys()
    {
        $assignment = $this->getAssignment();
        $assignmentKeys = array_keys($assignment);
        return $assignmentKeys;
    }

    public function setNumberOfExercises($numberOfExercises)
    {
        $this->numberOfExercises = $numberOfExercises;
    }

    public function getNumberOfExercises()
    {
        return $this->numberOfExercises;
    }

    public function setResultSize($resultSize)
    {
        $this->resultSize = $resultSize;
    }

    public function getResultSize()
    {
        return $this->resultSize;
    }

    public function getLayout()
    {
        if(!isset($this->layout)){
            $this->layout = $this->getRandomLayout();
        }#if

        return $this->layout;
    }

////////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function populateResultPreview(DOMElement $tableNode)
    {

        $allSlices = $this->buildSlicesForLayout();

        $this->addTilesToTableNode($allSlices, $tableNode);
    }

    public function populateAssignmentContent(\DOMElement $assignmentTable)
    {
        $numberOfExercises = $this->getNumberOfExercises();

        $template = $assignmentTable->ownerDocument;

        $assignmentTableBody = $assignmentTable->getElementsByTagName('tbody')->item(0);
        /** @noinspection PhpUndefinedMethodInspection Method removeChildrenFromNode() is defined in the Template Class*/
        $template->removeChildrenFromNode($assignmentTableBody);

        for ($i = 1; $i <= $numberOfExercises; $i++) {
            // Add row to table
            $currentRow = $template->createElement('tr');
            $assignmentTableBody->appendChild($currentRow);

            // Number
            $numberCell = $template->createElement('td', $i);

            // @TODO: $assignmentCell and it's contents should be created outside of the loop and cloned here as the only thing that changes is the inputs "name" attribute
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

    public function populateAssignmentResult(DOMElement $tableNode)
    {
        $assignmentKeys = $this->getAssignmentKeys();

        $layout = $this->getLayout();
        $template = $tableNode->ownerDocument;

        $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
        /** @noinspection PhpUndefinedMethodInspection Method removeChildrenFromNode() is defined in the Template Class*/
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

    public function populateResultContent(DOMElement $tableNode)
    {
        $layout = $this->getLayout();
        $template = $tableNode->ownerDocument;
        $assignment = $this->getAssignment();
        $assignmentKeys = $this->getAssignmentKeys();

        $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
        /** @noinspection PhpUndefinedMethodInspection Method removeChildrenFromNode() is defined in the Template Class*/
        $template->removeChildrenFromNode($tableBodyNode);

        $layoutFlip = array_flip($layout);
        ksort($layoutFlip);
        foreach ($layoutFlip as $key => $value) {
            $value--;
            $assignmentKey = $assignmentKeys[$value];
            if (($key-1) % 6 === 0) {
                $currentRow = $template->createElement('tr');
                $tableBodyNode->appendChild($currentRow);
            }#if

            $tableCell = $template->createElement('td', $assignment[$assignmentKey]);
            $tableCell->setAttribute('class', 'answer');

            /** @noinspection PhpUndefinedVariableInspection */
            $currentRow->appendChild($tableCell);
        }#foreach
    }


//////////////////////////////// HELPER METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    function fetchAssignmentArray()
    {
        $assignment = array();

        $assignmentTemp = $_POST['assignment'];
        $outcomeTemp = $_POST['outcome'];

        for ($i = 1; $i <= $this->getNumberOfExercises(); $i++) {
            $tempAssignment = $assignmentTemp[$i];
            $tempOutcome = $outcomeTemp[$i];
            //@FIXME: This creates a bug where, if two or more assignment have the same value, we overwrite $assignmentKeys and reduce the actual amount of keys available later on (also messing up the order.
            $assignment[$tempAssignment] = $tempOutcome;
        }#for

        return $assignment;
    }

    function addTilesToTableNode($allSlices, DOMElement $tableNode)
    {
        $resultSize = $this->getResultSize();

        $template = $tableNode->ownerDocument;

        $tableBodyNode = $tableNode->getElementsByTagName('tbody')->item(0);
        /** @noinspection PhpUndefinedMethodInspection Method removeChildrenFromNode() is defined in the Template Class*/
        $template->removeChildrenFromNode($tableBodyNode);


        foreach ($allSlices as  $key => $value) {
            if ($key % 6 === 0) {
                // first cell of this row of 6
                $currentRow = $template->createElement('tr');
                $tableBodyNode->appendChild($currentRow);
            }#if

            createTile($resultSize, $value);

            // @TODO: $tableCell and $image should be created outside of the loop and cloned here as the only thing that changes is the images "src" attribute
            $tableCell = $template->createElement('td');
            $tableCell->setAttribute('class', 'result');

            $image = $template->createElement('img');
            $image->setAttribute('src', 'images/'.$value.'.png');

            /** @noinspection PhpUndefinedVariableInspection */
            $currentRow->appendChild($tableCell);
            $tableCell->appendChild($image);
        }#foreach
    }

    function buildSlicesForLayout()
    {
        $layout = $this->getLayout();

        $layoutFlip = array_flip($layout);
        ksort($layoutFlip);
        //flip rows..
        $upperRow = array_slice($layoutFlip, 6, 6);
        $lowerRow = array_slice($layoutFlip, 0, 6);

        $allSlices = array_merge($upperRow, $lowerRow);

        return $allSlices;
    }
}

#EOF