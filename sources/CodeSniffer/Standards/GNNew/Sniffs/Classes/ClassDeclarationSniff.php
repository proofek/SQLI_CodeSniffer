<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

/**
 * GNNew_Sniffs_Classes_ClassDeclarationSniff
 * Stripped version of Zend_Sniffs_Classes_ClassDeclarationSniff
 *
 * Une seule classe ou une seule interface par fichier
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GNNew_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS, T_INTERFACE);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param  integer              $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Check that this is the only class or interface in the file
        $stackPtr = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), ($stackPtr + 1));
        
        if ($stackPtr !== false) {        	        
            // We have another, so an error is thrown
            $error = 'Un fichier source PHP doit contenir une seule classe (ou interface).';
            $phpcsFile->addError($error, $stackPtr, 'MultipleClassOrInterfaceInSingleFile');            
        }
    }
}
