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
 * ZF_Tests_Commenting_FileCommentUnitTest
 *
 * Checks the declaration of the class and its inheritance is correct
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZF_Tests_Commenting_FileCommentUnitTest extends AbstractSniffUnitTest
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
	        case 'FileCommentUnitTest.1.inc':
	        	return array(
	        		1 => 1,
	        	);
	        	break;
	        case 'FileCommentUnitTest.2.inc':
	        	return array(
	        		1 => 1,
	        	);
	        	break;
	        case 'FileCommentUnitTest.3.inc':
	        	return array(
	        		1 => 1,
	        	);
	        	break;
	        case 'FileCommentUnitTest.4.inc':
	        	return array(
	        		1 => 1,
	        	);
	        	break;
	        case 'FileCommentUnitTest.5.inc':
	        	return array(
	        		2 => 1,
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
    public function getWarningList()
    {         
    	return array();
    }//end getWarningList()

}//end class
?>