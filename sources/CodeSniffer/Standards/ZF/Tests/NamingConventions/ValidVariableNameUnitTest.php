<?php
/**
 * Checkstyle report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category   PHP_CodeSniffer_Standard
 * @package    PHP_CodeSniffer_Standard_ZF
 * @subpackage PHP_CodeSniffer_Standard_ZF_Tests_NamingConventions
 * @author     Sébastien Roux <seroux@sqli.com>
 * @author     Gabriele Santini <gsantini@sqli.com>
 * @author     Thomas Weidner <seroux@sqli.com>
 * @copyright  2010 SQLI <www.sqli.com>
 * @license    http: ???
 * @version    CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
 * @link       http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * ZF_Tests_NamingConventions_ValidVariableNameUnitTest
 *
 * Checks the declaration of the class and its inheritance is correct
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZF_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                3  => 1,
                5  => 1,
                11 => 1,
                13 => 1,
                17 => 1,
                19 => 1,
                23 => 1,
                25 => 1,
                29 => 1,
                31 => 1,
                36 => 1,
                38 => 1,
                42 => 1,
                44 => 1,
                48 => 1,
                50 => 1,
                61 => 1,
                67 => 1,
                72 => 1,
                74 => 1,
                75 => 1,
                76 => 1,
                79 => 1,
                90 => 1,
                92 => 1,
                96 => 1,
                99 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array(
                6  => 1,
                14 => 1,
                20 => 1,
                26 => 1,
                32 => 1,
                39 => 1,
                45 => 1,
                51 => 1,
                64 => 1,
                70 => 1,
                73 => 1,
                76 => 1,
                79 => 1,
                82 => 1,
                94 => 1,
               );

    }//end getWarningList()


}//end class

?>
