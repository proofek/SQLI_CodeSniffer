<?php
/**
 * ZF_Sniffs_Files_LineLengthSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_Files_LineLengthSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Files_LineLengthSniff not found');
}

/**
 * ZF_Sniffs_Files_LineLengthSniff.
 *
 * Checks all lines in the file, and throws warnings if they are over 80
 * characters in length and errors if they are over 120.
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class ZF_Sniffs_Files_LineLengthSniff extends Generic_Sniffs_Files_LineLengthSniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    protected $lineLimit = 80;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var int
     */
    protected $absoluteLineLimit = 120;

}//end class

?>
