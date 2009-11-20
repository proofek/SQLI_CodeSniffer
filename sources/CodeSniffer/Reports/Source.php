<?php
class SQLI_CodeSniffer_Reports_Source
{
    /**
     * Prints the source of all errors and warnings.
     *
     * @param boolean $showWarnings Show warnings as well as errors.
     * @param boolean $showSources  Show error sources in report.
     *
     * @return int The number of error and warning messages shown.
     */
    public function generate($report, $showLevel=SQLI_CodeSniffer_Reports::WARNING, $showSources=false)
    {
        $sources = array();

        $errorsShown = 0;

        $report = $this->prepareErrorReport($showWarnings);
        foreach ($report['files'] as $filename => $file) {
            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $errorsShown++;

                        $source = $error['source'];
                        if (isset($sources[$source]) === false) {
                            $sources[$source] = 1;
                        } else {
                            $sources[$source]++;
                        }
                    }
                }
            }//end foreach
        }//end foreach

        if ($errorsShown === 0) {
            // Nothing to show.
            return 0;
        }

        asort($sources);
        $sources = array_reverse($sources);

        echo PHP_EOL.'PHP CODE SNIFFER VIOLATION SOURCE SUMMARY'.PHP_EOL;
        echo str_repeat('-', 80).PHP_EOL;
        if ($showSources === true) {
            echo 'SOURCE'.str_repeat(' ', 69).'COUNT'.PHP_EOL;
            echo str_repeat('-', 80).PHP_EOL;
        } else {
            echo 'STANDARD    CATEGORY            SNIFF'.str_repeat(' ', 38).'COUNT'.PHP_EOL;
            echo str_repeat('-', 80).PHP_EOL;
        }

        foreach ($sources as $source => $count) {
            if ($showSources === true) {
                $source = substr($source, 0, -5);
                echo $source.str_repeat(' ', (75 - strlen($source)));
            } else {
                $parts = explode('.', $source);

                if (strlen($parts[0]) > 10) {
                    $parts[0] = substr($parts[0], 0, ((strlen($parts[0]) -10) * -1));
                }
                echo $parts[0].str_repeat(' ', (12 - strlen($parts[0])));

                $category = $this->makeFriendlyName($parts[1]);
                if (strlen($category) > 18) {
                    $category = substr($category, 0, ((strlen($category) -18) * -1));
                }
                echo $category.str_repeat(' ', (20 - strlen($category)));

                $sniff = substr($parts[2], 0, -5);
                $sniff = $this->makeFriendlyName($sniff);
                if (strlen($sniff) > 41) {
                    $sniff = substr($sniff, 0, ((strlen($sniff) - 41) * -1));
                }
                echo $sniff.str_repeat(' ', (43 - strlen($sniff)));
            }

            echo $count.PHP_EOL;
        }//end foreach

        echo str_repeat('-', 80).PHP_EOL;
        echo "A TOTAL OF $errorsShown SNIFF VIOLATION(S) ";
        echo 'WERE FOUND IN '.count($sources).' SOURCE(S)'.PHP_EOL;
        echo str_repeat('-', 80).PHP_EOL.PHP_EOL;

        return $errorsShown;

    }//end printSourceReport()


    /**
     * Converts a camel caps name into a readable string.
     *
     * @param string $name The camel caps name to convert.
     *
     * @return string
     */
    public function makeFriendlyName($name)
    {
        $friendlyName = '';
        $length = strlen($name);

        $lastWasUpper   = false;
        $lastWasNumeric = false;
        for ($i = 0; $i < $length; $i++) {
            if (is_numeric($name[$i]) === true) {
                if ($lastWasNumeric === false) {
                    $friendlyName .= ' ';
                }

                $lastWasUpper   = false;
                $lastWasNumeric = true;
            } else {
                $lastWasNumeric = false;

                $char = strtolower($name[$i]);
                if ($char === $name[$i]) {
                    // Lowercase.
                    $lastWasUpper = false;
                } else {
                    // Uppercase.
                    if ($lastWasUpper === false) {
                        $friendlyName .= ' ';
                        $next = $name[($i + 1)];
                        if (strtolower($next) === $next) {
                            // Next char is lowercase so it is a word boundary.
                            $name[$i] = strtolower($name[$i]);
                        }
                    }

                    $lastWasUpper = true;
                }
            }//end if

            $friendlyName .= $name[$i];
        }//end for

        $friendlyName    = trim($friendlyName);
        $friendlyName[0] = strtoupper($friendlyName[0]);

        return $friendlyName;

    }//end makeFriendlyName()
}
