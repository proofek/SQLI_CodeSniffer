<?php
/**
 * An abstract class that all sniff unit tests must extend.
 *
 * Based on the equivalent class in PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: AbstractSQLISniffUnitTest.php,v 1.14 2009/01/29 23:38:35 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__).'/SortableSniffUnitTest.php';

/**
 * An abstract class that all sniff unit tests must extend.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings that are not found, or
 * warnings and errors that are not expected, are considered test failures.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class AbstractSQLISniffUnitTest extends PHPUnit_Framework_TestCase
{

    /**
     * The SQLI_CodeSniffer object used for testing.
     *
     * @var SQLI_CodeSniffer
     */
    protected static $phpcs = null;


    /**
     * Sets up this unit test.
     *
     * @return void
     */
    protected function setUp()
    {
        if (self::$phpcs === null) {
            self::$phpcs = new SQLI_CodeSniffer();
        }

    }//end setUp()


    /**
     * Should this test be skipped for some reason.
     *
     * @return void
     */
    protected function shouldSkipTest()
    {
        return false;

    }
    
    protected function isSortable() {
        return $this instanceof SortableSniffUnitTest;
    }

    /**
     * Get a list of all test files to check.
     * 
     * Dummy files must be in a Files/ directory on the same level of the test.
     * They must begin with the same prefix than the test.
     * 
     * @param string $basename
     * @param string $standardName
     * @return array
     */
    protected function getTestFiles($basename, $standardName)
    {   
        if (is_file(dirname(__FILE__).'/../../CodeSniffer.php') === true) {
            // We have not been installed.
            $standardsDir = realpath(dirname(__FILE__).'/../../CodeSniffer/Standards');
            $testFileBase = $standardsDir.'/'.str_replace('_', '/', $basename).'UnitTest.';
        } else {
            // The name of the dummy file we are testing.
            // TODO : fix this with package.xml !
            $testFileBase = dirname(__FILE__).'/../../CodeSniffer/Standards/'.str_replace('_', '/', $basename).'UnitTest.';
        }

        $testFilesDir = substr($testFileBase, 0, strrpos($testFileBase, '/')) . '/Files';
        $testFilesPrefix = substr($testFileBase, strrpos($testFileBase, '/') +1, -1);

        $testFiles = array();
        $di  = new DirectoryIterator($testFilesDir);
        foreach ($di as $element) {
            if ($element->isFile() && strpos($element->getFilename(), $testFilesPrefix) === 0) {
                $testFiles[] = $element->getPathname();
            }
        }
        
        // Get test files in order if they are intended to. 
        // This is particularly important for multi-file sniffs.
        if ($this->isSortable()) {
            $this->sortTestFiles($testFiles);
        }

        return $testFiles;
    }
    
    /**
     * Tests the extending classes Sniff class.
     *
     * @return void
     * @throws PHPUnit_Framework_Error
     */
    protected final function runTest()
    {
        // Skip this test if we can't run in this environment.
        if ($this->shouldSkipTest() === true) {
            $this->markTestSkipped();
        }
        
        // The basis for determining file locations.
        $basename = substr(get_class($this), 0, -8);

        // The name of the coding standard we are testing.
        $standardName = substr($basename, 0, strpos($basename, '_'));
        
        $testFiles = $this->getTestFiles($basename, $standardName);

        // The class name of the sniff we are testing.
        $sniffClass = str_replace('_Tests_', '_Sniffs_', $basename).'Sniff';        
        
        $failureMessages = array();
        $multiFileSniff  = false;
        foreach ($testFiles as $testFile) {
            try {
                self::$phpcs->process($testFile, $standardName, array($sniffClass));
            } catch (Exception $e) {
                $this->fail('An unexpected exception has been caught: '.$e->getMessage());
            }

            // After processing a file, check if the sniff was actually
            // a multi-file sniff (i.e., had no indivdual file sniffs).
            // If it is, we can skip checking of the other files and
            // do a single multi-file check.
            $sniffs = self::$phpcs->getTokenSniffs();
            if (empty($sniffs['file']) === true) {
                $multiFileSniff = true;
                break;
            }

            $files = self::$phpcs->getFiles();
            $file  = array_pop($files);

            $failures = $this->generateFailureMessages($file, $testFile);
            $failureMessages = array_merge($failureMessages, $failures);
        }//end foreach

        if ($multiFileSniff === true) {
            try {
                self::$phpcs->process($testFiles, $standardName, array($sniffClass));
            } catch (Exception $e) {
                $this->fail('An unexpected exception has been caught: '.$e->getMessage());
            }

            $files = self::$phpcs->getFiles();
            foreach ($files as $file) {
                $failures = $this->generateFailureMessages($file);
                $failureMessages = array_merge($failureMessages, $failures);
            }
        }

        if (empty($failureMessages) === false) {
            $this->fail(implode(PHP_EOL, $failureMessages));
        }

    }
    
    /**
     * Add events in $eventList1 that are not in $eventList2 to $errors array.
     * 
     * Works by reference on $errors by adding $message errors.
     * 
     * @param SQLI_CodeSniffer_EventList $eventList1
     * @param SQLI_CodeSniffer_EventList $eventList2
     * @param string $message
     * @param array $errors
     * @return void
     */
    protected static function _getMissingEvents($eventList1, $eventList2, $message, &$errors) 
    {
        if (count($eventList1) == 0) {
            return;
        }
        foreach ($eventList1 as $event) {
            if (!$eventList2->hasEvent($event)) {
                $errors[$event->getLine()][$event->getColumn()][] = str_replace(':code', $event->getCode(), $message);
            }
        }
    }
    
    /**
     * Sort $errors by lines and columns.
     * 
     * Works by reference on $errors.
     * 
     * @param $errors
     * @return void
     */
    protected static function _sortErrors(&$errors)
    {        
        foreach ($errors as &$lineError) {
            ksort($lineError);
        }
        ksort($errors);
    }
    
    
    /**
     * Generate a list of test failures for a given sniffed file.
     *
     * @param SQLI_CodeSniffer_File $file
     * @return array
     * @throws SQLI_CodeSniffer_Exception
     */
    public function generateFailureMessages($file)
    {
        $testFile = $file->getFilename();

        $foundEvents    = $file->getEventList();
        $expectedEvents = $this->getEventList(basename($testFile));
        $allErrors = array();
       
        $expectedNotFound = "Found unexpected event of code ':code'";
        self::_getMissingEvents($foundEvents, $expectedEvents, $expectedNotFound, $allErrors);
        
        $foundNotExpected = "Expected event of code ':code' not found";
        self::_getMissingEvents($expectedEvents, $foundEvents, $foundNotExpected, $allErrors);
        
        self::_sortErrors($allErrors);

        $failureMessages = array();
        if (count($allErrors)) {
            $failureMessages[] =  'Failure analyzing '. basename($testFile);
            foreach ($allErrors as $line => $lineError) {
                foreach ($lineError as $column => $errorArray) {
                    $errorMessage = implode(PHP_EOL . ' -> ', $errorArray);
                    $failureMessages[] = "[LINE $line][COLUMN $column]: $errorMessage";  
                }
            }
        }

        return $failureMessages;
    }

    /**
     * Returns expected events list.
     *
     * @param string $testFile name of dummy test file
     * @return SQLI_CodeSniffer_EventList
     */
    protected abstract function getEventList($testFile = '');

}