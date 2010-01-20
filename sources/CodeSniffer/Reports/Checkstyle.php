<?php
class SQLI_CodeSniffer_Reports_Checkstyle
{

    /**
     * Prints all violations for processed files, in a Checkstyle format.
     *
     * Violations are grouped by file.
     *
     * @param array $report Show warnings as well as errors.
     * @param boolean $showSources
     *
     * @return int The number of violation messages shown.
     */
    public function generate($report, $showSources=false)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<checkstyle version="@package_version@">'.PHP_EOL;

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            echo ' <file name="'.$filename.'">'.PHP_EOL;

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $error['type'] = strtolower($error['type']);
                        echo '  <error';
                        echo ' line="'.$line.'" column="'.$column.'"';
                        echo ' severity="'.$error['type'].'"';
                        echo ' code="'.$error['code'].'"';
                        $message = htmlspecialchars($error['message'], ENT_COMPAT, 'UTF-8');
                        echo ' message="'.$message.'"';
                        echo ' source="'.$error['source'].'"';
                        echo '/>'.PHP_EOL;
                        $errorsShown++;
                    }
                }
            }//end foreach

            echo ' </file>'.PHP_EOL;
        }//end foreach

        echo '</checkstyle>'.PHP_EOL;

        return $errorsShown;

    }
}