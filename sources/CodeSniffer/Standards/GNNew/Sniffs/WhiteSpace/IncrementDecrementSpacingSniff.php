<?php
/**
 * GNNew_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @copyright 2008 SQLI
 * 
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * GNNew_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff.
 *
 * Vérifie l'absence d'espace entre les opérateurs "++", "--" et leur variable
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @copyright 2008 SQLI
 * 
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GNNew_Sniffs_WhiteSpace_IncrementDecrementSpacingSniff implements PHP_CodeSniffer_Sniff
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
     * Lance le sniff, lorsqu'un des types de segments est détecté
     *
     * @param PHP_CodeSniffer_File $phpcsFile Le fichier actuellement analysé.
     * @param int                  $stackPtr  La position du segment actuel dans la pile $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $operator = $tokens[$stackPtr]['content'];
        
        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
		    $phpcsFile->addError('Aucun espace attendu avant l\'opérateur '.$operator , $stackPtr, 'SpaceBeforeDecrement');
        }
    }
}

?>
