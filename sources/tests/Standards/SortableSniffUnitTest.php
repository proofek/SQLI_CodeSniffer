<?php
/**
 * An interface for sniffer testers that need ordered test files. 
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: AbstractSniffUnitTest.php,v 1.14 2009/01/29 23:38:35 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * An interface for sniffer testers that need ordered test files. 
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings that are not found, or
 * warnings and errors that are not expected, are considered test failures.
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @copyright 2009 SQLI 
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
interface SortableSniffUnitTest
{
    /**
     * Order test files for parsing.
     * 
     * @param $testFiles
     * @return void
     */
    public static function sortTestFiles(array &$testFiles);
}