<?php
/**
 * Tests for the SQLI_CodeSniffer_CLI methods.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: IsCamelCapsTest.php,v 1.5 2007/08/02 00:05:40 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Tests for the SQLI_CodeSniffer_CLI methods.
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
class Core_CLITest extends PHPUnit_Framework_TestCase
{
    /**
     * Called class
     *
     * @var SQLI_CodeSniffer_CLI
     */
    protected $cli;
    
    
    public function setUp()
    {
        $this->cli = new SQLI_CodeSniffer_CLI();
    }

    /**
     * Test printUsage method output.
     *
     * @return void
     */
    public function testPrintUsage()
    {
        
        ob_start();
        $this->cli->printUsage();
        $usage = ob_get_clean();
        $this->assertContains('sqlics', $usage);
        $this->assertNotContains('phpcs', $usage);
    }
    
    public function testGetDefaults()
    {   
        
        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA']['show_warnings'] = true;
        $defaults = $this->cli->getDefaults();
        $this->assertEquals(SQLI_CodeSniffer_Reports::WARNING, $defaults['showLevel'], 'if I have showWarnings showLevel is WARNING');

        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA']['show_warnings'] = false;
        $defaults = $this->cli->getDefaults();
        $this->assertEquals(SQLI_CodeSniffer_Reports::ERROR, $defaults['showLevel'], 'if I have no showWarnings showLevel is ERROR');
        
        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA']['show_level'] = SQLI_CodeSniffer_Reports::INFO;
        $defaults = $this->cli->getDefaults();
        $this->assertEquals(SQLI_CodeSniffer_Reports::INFO, $defaults['showLevel'], 'config show_level is considered');
    }
    
    public function testProcessLongArgument()
    {
        $this->markTestIncomplete();
    }
    
    public function testPrintReport()
    {
        $this->markTestIncomplete();
    }
}