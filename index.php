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
require 'class.Generator.php';

$generator = new Generator();
//------------------------------------------------------------------------------
// Load Template
$template = \Potherca\Template::fromFile('index.template.html');
// Build Relevant Content Blocks

if (empty($_POST['assignment'])){
    //no input so display form

    // Remove Views we don't want
    $template->getBody()->removeChild(
        $template->getFirstElementWithClassName('result-view')
    );

    // Fetch nodes we'll work with
    $resultTable = $template->getFirstElementWithClassName('result-preview');
    $assignmentTable = $template->getFirstElementWithClassName('assignment');

    // Build preview
    $generator->populateResultPreview($resultTable);

    // Add Assignment Form to Template
    $generator->populateAssignmentContent($assignmentTable);
} else {
    //input so display result

    // Remove Views we don't want
    $template->getBody()->removeChild(
        $template->getFirstElementWithClassName('form-view')
    );

    // Fetch nodes we'll work with
    $assignmentResultTable = $template->getFirstElementWithClassName('assignment-result');
    $resultTable = $template->getFirstElementWithClassName('images-result');
    $answersResultTable = $template->getFirstElementWithClassName('answers-result');

    // Add Assignment Result to Template
    $generator->populateAssignmentResult($assignmentResultTable);

    // Build result
    $generator->populateResultPreview($resultTable);

    //show the answers
    $generator->populateResultContent($answersResultTable);
}#if

// Output Template
echo $template;
//------------------------------------------------------------------------------

#EOF