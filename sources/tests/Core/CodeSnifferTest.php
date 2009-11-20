<?php
/**
 * Tests for the PHP_CodeSniffer:isCamelCaps method.
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
 * Tests for the PHP_CodeSniffer:isCamelCaps method.
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
class Core_CodeSnifferTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test valid public function/method names.
     *
     * @return void
     */
    public function testAutoload()
    {
        try {
            $object = new SQLI_CodeSniffer();
            $this->assertTrue ($object instanceof SQLI_CodeSniffer );
        } catch (Exception $e) {
            $this->fail('Autoload an SQLI_CodeSniffer Core class');
        }
        try {
            $object = new PHP_CodeSniffer_Exception("test");
            $this->assertTrue ($object instanceof PHP_CodeSniffer_Exception);
        } catch (Exception $e) {
            $this->fail('Autoload a PHP_CodeSniffer Core class ' . $e->getMessage());
        }
        try {
            $object = new Generic_Sniffs_Classes_DuplicateClassNameSniff();
            // trick to recognize SQLI implemented sniffer
            $method = new ReflectionMethod('Generic_Sniffs_Classes_DuplicateClassNameSniff', 'process');
            $this->assertContains('SQLI_CodeSniffer_File', $method->getDocComment());
        } catch (Exception $e) {
            $this->fail('Autoload an SQLI_CodeSniffer sniffer');
        }
        try {
            $object = new Generic_Sniffs_CodeAnalysis_UnnecessaryFinalModifierSniff();
            // trick to recognize classical sniffer
            $method = new ReflectionMethod('Generic_Sniffs_CodeAnalysis_UnnecessaryFinalModifierSniff', 'process');
            $this->assertContains('PHP_CodeSniffer_File', $method->getDocComment());
        } catch (Exception $e) {
            $this->fail('Autoload a PHP_CodeSniffer sniffer');
        }
    }
}