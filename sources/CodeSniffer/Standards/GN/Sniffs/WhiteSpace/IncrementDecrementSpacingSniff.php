<?php
/**
 * GN_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @copyright 2008 SQLI
 * 
     * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
     * @version   CVS: $Id: OperatorSpacingSniff.php,v 1.10 2007/07/23 01:47:54 squiz Exp $
     * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * GN_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff.
 *
 * V�rifie l'absence d'espace entre les op�rateurs "++", "--" et leur variable
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @copyright 2008 SQLI
 * 
     * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
     * @version   Release: 1.1.0
     * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GN_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff implements SQLI_CodeSniffer_Sniff
{
    /**
     * Retourne un tableau des types de segments pour lesquels il faut lancer le test.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INC,
                T_DEC,
               );
    }


    /**
     * Lance le sniff, lorsqu'un des types de segments est d�tect�
     *
     * @param PHP_CodeSniffer_File $phpcsFile Le fichier actuellement analys�.
     * @param int                  $stackPtr  La position du segment actuel dans la pile $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $operator = $tokens[$stackPtr]['content'];
        
        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
		    $phpcsFile->addEvent(
		       'EXPECTED_SPACE_DECREMENTSPACING', 
		       array('operator' => $operator),
		       $stackPtr
		    );        	
        }
    }
}

?>
