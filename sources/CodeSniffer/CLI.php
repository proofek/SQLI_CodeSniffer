<?php
/**
 * A class to process command line phpcs scripts.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: CLI.php,v 1.9 2008/12/21 23:54:14 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'SQLI/CodeSniffer.php';
}


/**
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class SQLI_CodeSniffer_CLI extends PHP_CodeSniffer_CLI
{

    /**
     * Prints out the usage information for this script.
     *
     * @return void
     */
    public function printUsage()
    {
        ob_start();
        parent::printUsage();
        $usage = ob_get_clean();
        $usage = str_replace('phpcs', 'sqlics', $usage);
        $first_eol = strpos($usage, PHP_EOL);
        $first_line = substr($usage, 0, $first_eol);
        $usage = str_replace($first_line, $first_line . ' [--level=<level>]', $usage);
        $usage .= '        <level>      Cut the report severities to one of these values:' . PHP_EOL;
        $usage .= '                     emergency, alert, critical, error, warning, notice, info, debug' . PHP_EOL;
        echo $usage;
    }
    
    /**
     * Runs PHP_CodeSniffer over files are directories.
     * 
     * Duplicated to allow SQLI_CodeSniffer instanciation
     *
     * @param array $values An array of values determined from CLI args.
     *
     * @return int The number of error and warning messages shown.
     * @see getCommandLineValues()
     */
    public function process($values=array())
    {
        if (empty($values) === true) {
            $values = $this->getCommandLineValues();
        }

        if ($values['generator'] !== '') {
            $phpcs = new SQLI_CodeSniffer($values['verbosity']);
            $phpcs->generateDocs(
                $values['standard'],
                $values['files'],
                $values['generator']
            );
            exit(0);
        }

        if (empty($values['files']) === true) {
            echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $values['standard'] = $this->validateStandard($values['standard']);
        if (SQLI_CodeSniffer::isInstalledStandard($values['standard']) === false) {
            // They didn't select a valid coding standard, so help them
            // out by letting them know which standards are installed.
            echo 'ERROR: the "'.$values['standard'].'" coding standard is not installed. ';
            $this->printInstalledStandards();
            exit(2);
        }

        $phpcs = new SQLI_CodeSniffer($values['verbosity'], $values['tabWidth']);

        // Set file extensions if they were specified. Otherwise,
        // let PHP_CodeSniffer decide on the defaults.
        if (empty($values['extensions']) === false) {
            $phpcs->setAllowedFileExtensions($values['extensions']);
        }

        // Set ignore patterns if they were specified.
        if (empty($values['ignored']) === false) {
            $phpcs->setIgnorePatterns($values['ignored']);
        }

        $phpcs->process(
            $values['files'],
            $values['standard'],
            $values['sniffs'],
            $values['local']
        );

        return $this->printReport(
            $phpcs,
            $values['standard'],
            $values['report'],
            $values['showLevel'],
            $values['showSources'],
            $values['reportFile']
        );

    }//end process()
    

    /**
     * Get a list of default values for all possible command line arguments.
     *
     * @return array
     */
    public function getDefaults()
    {
        $defaults = parent::getDefaults();
        $showLevel = PHP_CodeSniffer::getConfigData('show_level');
        if ($showLevel === null) {
            if ($defaults['showWarnings']) {
                $defaults['showLevel'] = SQLI_CodeSniffer_Reports::WARNING;
            } else {
                $defaults['showLevel'] = SQLI_CodeSniffer_Reports::ERROR;
            }
        } else {
            $defaults['showLevel'] = $showLevel;
        }

        return $defaults;

    }//end getDefaults()
    
    /**
     * Processes a long (--example) command line argument.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processLongArgument($arg, $pos, $values)
    {

        if (substr($arg, 0, 6) === 'level=') {
            $showLevel = strtoupper(substr($arg, 6));
            if (defined('SQLI_CodeSniffer_Reports::' . $showLevel)) {
                $values['showLevel'] = constant('SQLI_CodeSniffer_Reports::' . $showLevel);
            }
        } elseif ($arg == 'version') {
            echo 'SQLI_CodeSniffer version @package_version@ (alpha) ';
            echo 'by SQLI (http://www.sqli.com)'.PHP_EOL;
            exit(0);
        } else {
            $values = parent::processLongArgument($arg, $pos, $values);
        }
        
        return $values;
    }
    
    
    /**
     * Prints the error report.
     * 
     * 
     *
     * @param PHP_CodeSniffer $phpcs        The PHP_CodeSniffer object containing
     *                                      the errors.
     * @param string          $standard     Standard to use.
     * @param string          $report       The type of report to print.
     * @param bool            $showLevel    Cut level for this report
     * @param bool            $showSources  TRUE if the report should show error sources
     *                                      (not used by all reports).
     * @param string          $reportFile   A file to log the report out to.
     *
     * @return int The number of error and warning messages shown.
     */
    public function printReport($phpcs, $standard, $report, $showLevel, $showSources, $reportFile='')
    {
        $reportManager = new SQLI_CodeSniffer_Reports(); 
        $reportClass = $reportManager->factory($report);
        $fileEvents  = $phpcs->getFilesEvents();
        $reportData  = $reportManager->prepareErrorReport($fileEvents, $standard, $showLevel);
                
        if ($reportFile !== '') {
            ob_start();
        }
        
        $numErrors = $reportClass->generate($reportData, $showSources);
        
        if ($reportFile !== '') {
            $generatedReport = ob_get_clean();            
            $generatedReport = trim($generatedReport);
            file_put_contents($reportFile, "$generatedReport\n");
        }

        return $numErrors;

    }
}