<?php
/**
 * Tests for the SQLI_CodeSniffer_Reports methods.
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
require_once dirname(__FILE__) . '/../Stubs/SQLI_CodeSniffer_ReportsStub.php';
/**
 * Tests for the SQLI_CodeSniffer_Reports methods.
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
class Core_ReportsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Enter description here...
     *
     * @var SQLI_CodeSniffer_Reports
     */
    protected $reports;

    
    public function setUp()
    {
        $this->reports = new SQLI_CodeSniffer_ReportsStub();
    }

    
    public function testFactory()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * Test config-related issues
     *
     */
    public function testConfig()
    {
        $filename = dirname(__FILE__) . '/Data/config.xml';
        $this->reports->setConfig($filename);
        
        // set event
        $paramArray = array(
            'type' => 'Type',
            'name' => 'Name',
            'file' => 'File',
            'line' => '120'
        );
        $event = new SQLI_CodeSniffer_Event(1, 1, 'DUPLICATE_CLASS_OR_INTERFACE', $paramArray);
        list($eventLevel, $eventMessage) = $this->reports->getEventLevelAndMessage($event);
        $this->assertEquals('ERROR', $eventLevel,'Level configuration is correctedly setted');
        $this->assertEquals('Duplicate :type name ":name" found; first defined in :file on line :line', $eventMessage, 'Message configuration is correctedly setted');
    }
    
    public function testSetEventsInfos()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetErrors()
    {
        $this->markTestIncomplete();
    }
    
    public function testPrepareErrorReport()
    {
        $this->markTestIncomplete();
    }
    

}