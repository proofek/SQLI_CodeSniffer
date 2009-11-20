<?php
/**
 * A test class for testing the core.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: AllTests.php,v 1.4 2007/07/23 01:47:54 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'CodeSnifferTest.php';
require_once 'CLITest.php';
require_once 'FileTest.php';
require_once 'EventListTest.php';
require_once 'ReportsTest.php';

/**
 * A test class for testing the core.
 *
 * Do not run this file directly. Run the AllSniffs.php file in the root
 * testing directory of PHP_CodeSniffer.
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
class SQLI_CodeSniffer_Core_AllTests
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnits_TextUI_TestRunner::run(self::suite());
    }


    /**
     * Add all core unit tests into a test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('SQLI CodeSniffer Core');
        $suite->addTestSuite('Core_CodeSnifferTest');
        $suite->addTestSuite('Core_CLITest');
        $suite->addTestSuite('Core_FileTest');
        $suite->addTestSuite('Core_EventListTest');
        $suite->addTestSuite('Core_ReportsTest');
        
        return $suite;

    }

}