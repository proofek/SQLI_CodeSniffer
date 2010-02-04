<?php
/**
 * Checkstyle report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category   PHP_CodeSniffer_Standard
 * @package    PHP_CodeSniffer_Standard_ZF
 * @subpackage PHP_CodeSniffer_Standard_ZF_Tests_Commenting
 * @author     Sébastien Roux <seroux@sqli.com>
 * @author     Gabriele Santini <gsantini@sqli.com>
 * @author     Thomas Weidner <seroux@sqli.com>
 * @copyright  2010 SQLI <www.sqli.com>
 * @license    http: ???
 * @version    CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
 * @link       http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * ZF_Tests_Commenting_ClassCommentUnitTest
 *
 * Checks the declaration of the class and its inheritance is correct
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZF_Tests_Commenting_ClassCommentUnitTest extends AbstractSniffUnitTest
{
	
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
	        case 'ClassCommentUnitTest.1.inc':
	        	return array(
	        		6 => 1,
	        		14 => 1,
	        		19 => 1,
	        	);
	        	break;
	        case 'ClassCommentUnitTest.2.inc':
	        	return array(
	        		3 => 1,
	        		4 => 1,
	        		12 => 1,
	        		21 => 2,
	        		25 => 1,
	        		26 => 1,
	        		37 => 2,
	        		55 => 1,
	        		57 => 1,
	        		66 => 2,
	        		67 => 1,
	        		68 => 1,
	        		69 => 1,
	        		70 => 1,
	        		71 => 1,
	        		73 => 1,
	        		74 => 1,
	        		75 => 1,
	        		82 => 3,
	        		87 => 1,
	        		89 => 1,
	        		92 => 1,
	        		93 => 1,
	        	);
	        	break;       
	        default:
	            return array();
	            break;
        }  	
    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList($testFile='')
    {
         switch ($testFile) {	        
	        case 'ClassCommentUnitTest.2.inc':
	        	return array(
	        		40 => 1,
	        	);
	        	break;
	        default:
	            return array();
	            break;	        	
         }
    }//end getWarningList()

}//end class
?>