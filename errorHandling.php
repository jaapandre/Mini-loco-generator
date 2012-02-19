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
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
function errorToException($errorNumber, $message, $fileName, $lineNumber ) {
    // ignore DOMDocument::loadHTML errors
    if (error_reporting() !== 0 && strpos($message, 'DOMDocument::loadHTML') !== 0)
    {
        throw new ErrorException($message, $errorNumber, $errorNumber, $fileName, $lineNumber);
    }
};

function catchFatalErrors(){
    $errorArray = error_get_last();
    if ($errorArray != null)
    {
        if (ob_get_length() > 0)
        {
            // trap any content that may already have been created
            $output = ob_end_clean();
        }

        $exception = new ErrorException(
            $errorArray['message']
            , $errorArray['type']
            , $errorArray['type']
        );

        //@TODO: Read template and put in error message

        echo implode("\n", $errorArray);
    }#if
}

set_error_handler('errorToException');
register_shutdown_function('catchFatalErrors');
