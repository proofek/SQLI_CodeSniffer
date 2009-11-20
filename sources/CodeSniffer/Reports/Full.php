<?php
class SQLI_CodeSniffer_Reports_Full extends SQLI_CodeSniffer_Reports_Abstract
{

    /**
     * Prints all errors and warnings for each file processed.
     *
     * Errors and warnings are displayed together, grouped by file.
     *
     * @param boolean $showWarnings Show warnings as well as errors.
     * @param boolean $showSources  Show error sources in report.
     *
     * @return int The number of error and warning messages shown.
     */
    public function generate($report, $showSources=false)
    {
        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            if (empty($file['messages']) === true) {
                continue;
            }

            echo PHP_EOL.'FILE: ';
            if (strlen($filename) <= 71) {
                echo $filename;
            } else {
                echo '...'.substr($filename, (strlen($filename) - 71));
            }

            echo PHP_EOL;
            echo str_repeat('-', 80).PHP_EOL;
            if (count($file['events'] )) {
                echo 'FOUND : ';
                $typeLength = 0;
                foreach($file['events'] as $level => $eventCount) {
                    $typeLength = max($typeLength, strlen($level));
                    echo $eventCount . ' ' . strtoupper($level) . '(S) ';
                }
            }

            echo 'AFFECTING '.count($file['messages']).' LINE(S)'.PHP_EOL;
            echo str_repeat('-', 80).PHP_EOL;

            // Work out the max line number for formatting.
            $maxLine = 0;
            foreach ($file['messages'] as $line => $lineErrors) {
                if ($line > $maxLine) {
                    $maxLine = $line;
                }
            }

            $maxLineLength = strlen($maxLine);


            // The padding that all lines will require that are
            // printing an error message overflow.
            $paddingLine2  = str_repeat(' ', ($maxLineLength + 1));
            $paddingLine2 .= ' | ';
            $paddingLine2 .= str_repeat(' ', $typeLength);
            $paddingLine2 .= ' | ';

            // The maximum amount of space an error message can use.
            $maxErrorSpace = (79 - strlen($paddingLine2));

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $message = $error['message'];
                        if ($showSources === true) {
                            $message .= ' ('.substr($error['source'], 0, -5).')';
                        }

                        // The padding that goes on the front of the line.
                        $padding  = ($maxLineLength - strlen($line));
                        $errorMsg = wordwrap(
                            $message,
                            $maxErrorSpace,
                            PHP_EOL."$paddingLine2"
                        );

                        echo ' '.str_repeat(' ', $padding).$line.' | '.$error['type'];
                        $paddingType = $typeLength - strlen($error['type']);
                        echo str_repeat(' ', $paddingType);

                        echo ' | '.$errorMsg.PHP_EOL;
                        $errorsShown++;
                    }//end foreach
                }//end foreach
            }//end foreach

            echo str_repeat('-', 80).PHP_EOL.PHP_EOL;
        }//end foreach

        return $errorsShown;

    }
}