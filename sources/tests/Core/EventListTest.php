<?php
/**
 * Tests for the SQLI_CodeSniffer_EventList methods.
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
 * Tests for the SQLI_CodeSniffer_EventList methods.
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
class Core_EventListTest extends PHPUnit_Framework_TestCase
{
    protected $eventArray;
    protected $eventList;
    
    
    public function setup()
    {
        $this->eventArray = array(
            new SQLI_CodeSniffer_Event(3,2,'L3C2E0'),
            new SQLI_CodeSniffer_Event(2,3,'L2C3E0'),
            new SQLI_CodeSniffer_Event(5,1,'L5C2E1'),
            new SQLI_CodeSniffer_Event(3,2,'L3C2E1'),
            new SQLI_CodeSniffer_Event(3,1,'L3C1E0'),
            
        );
        $this->sortedArray = array(
            new SQLI_CodeSniffer_Event(2,3,'L2C3E0'),
            new SQLI_CodeSniffer_Event(3,1,'L3C1E0'),
            new SQLI_CodeSniffer_Event(3,2,'L3C2E0'),
            new SQLI_CodeSniffer_Event(3,2,'L3C2E1'),
            new SQLI_CodeSniffer_Event(5,1,'L5C2E1'),
        );
        $this->eventList = new SQLI_CodeSniffer_EventList($this->eventArray);        
    }
    
    
    /**
     * Test $_status relative issues
     *
     */
    public function testStatus()
    {
        $this->markTestIncomplete();
    }
    
    public function testAddEvent()
    {
        $this->markTestIncomplete();
    }
    
    public function testLevelCount()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetEvents()
    {
        $this->markTestIncomplete();
    }
    
    public function testCount()
    {
        $this->assertEquals(5, $this->eventList->count());
    }
    
    public function testForeach()
    {
        $i = 0;
        foreach($this->eventList as $event) {
            $this->assertEquals($this->sortedArray[$i], $event);
            $i++;
        }
    }

}