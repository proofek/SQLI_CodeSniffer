<?php
/**
 * A test class for testing all sniffs for installed standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: AllSniffs.php,v 1.7 2007/08/15 01:26:09 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

// Require this here so that the unit tests don't have to try and find the
// abstract class once it is installed into the PEAR tests directory.
require_once dirname(__FILE__).'/AbstractSniffUnitTest.php';
require_once dirname(__FILE__).'/AbstractSQLISniffUnitTest.php';
require_once dirname(__FILE__).'/AbstractTaggedSniffUnitTest.php';

/**
 * A test class for testing all sniffs for installed standards.
 *
 * Usage: phpunit AllSniffs.php
 *
 * This test class loads all unit tests for all installed standards into a
 * single test suite and runs them. Errors are reported on the command line.
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class SQLI_CodeSniffer_Standards_AllSniffs
{

    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());

    }


    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('SQLI CodeSniffer Standards');

        $isInstalled = !is_file(dirname(__FILE__).'/../../CodeSniffer.php');

        if ($isInstalled) {
            $standardsDir = '';
        } else {
            // We have not been installed.
            // TODO : this folders should be directly in Standards - fix with package.xml !
            $standardsDir = realpath(dirname(__FILE__).'/../../CodeSniffer/Standards');            
        }

        $standards = SQLI_CodeSniffer::getInstalledStandards(true, $standardsDir);

        foreach ($standards as $standard) {
            if ($isInstalled) {
                // TODO : this folders should be directly in Standards - fix with package.xml !
                $standardDir = realpath(dirname(__FILE__).'/../../CodeSniffer/Standards/'.$standard.'/Tests/');                
            } else {
                $standardDir = realpath($standardsDir.'/'.$standard.'/Tests/');
            }

            if (!is_dir($standardDir)) {
                // No tests for this standard.
                continue;
            }

            $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($standardDir));
            foreach ($di as $file) {
                // Skip hidden files.
                if (substr($file->getFilename(), 0, 1) === '.') {
                    continue;
                }

                // Tests must have the extention 'php'.
                $parts = explode('.', $file);
                $ext   = array_pop($parts);
                if ($ext !== 'php') {
                    continue;
                }

                $filePath = realpath($file->getPathname());

                if ($isInstalled === false) {
                    $className = str_replace($standardDir.DIRECTORY_SEPARATOR, '', $filePath);
                } else {
                    // TODO : fix this with package.xml !
                    $cutposition = strpos($filePath, '/CodeSniffer/Standards/');
                    $className = substr($filePath, $cutposition + strlen('/CodeSniffer/Standards/'));
                }

                $className = substr($className, 0, -4);
                $className = str_replace(DIRECTORY_SEPARATOR, '_', $className);

                if ($isInstalled === false) {
                    $className = $standard.'_Tests_'.$className;
                }

                $niceName  = substr($className, (strrpos($className, '_') + 1), -8);

                include_once $filePath;
                $class = new $className($niceName);
                $suite->addTest($class);
            }
        }

        return $suite;
    }

}