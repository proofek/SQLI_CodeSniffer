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
if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Zend_Sniffs_WhiteSpace_MemberVarSpacingSniff
 *
 * Verifies that class members are spaced correctly.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_WhiteSpace_MemberVarSpacingSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    /**
     * Processes the function tokens within the class.
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param  integer              $stackPtr  The position where the token was found.
     * @return void
     */
    public function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // There needs to be 1 blank line before the var, not counting comments.
        $prevLineToken = null;
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$commentTokens) === true) {
                // Skip comments.
                continue;
            } else if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                // Not the end of the line.
                continue;
            } else {
                // If this is a WHITESPACE token, and the token right before
                // it is a DOC_COMMENT, then it is just the newline after the
                // member var's comment, and can be skipped.
                if ($tokens[$i]['code'] === T_WHITESPACE and
                    in_array($tokens[($i - 1)]['code'], PHP_CodeSniffer_Tokens::$commentTokens) === true) {
                    continue;
                }

                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the member var.
            $foundLines = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(array(T_WHITESPACE, T_DOC_COMMENT), $prevLineToken, null, true);
            $foundLines  = ($tokens[$prevLineToken]['line'] - $tokens[$prevContent]['line']);
        }

        if ($tokens[$prevContent]['content'] === '{') {
            if ($foundLines !== 0) {
                $phpcsFile->addError("Expected 0 blank line before member var; $foundLines found", $stackPtr);
            }
        } else if ($foundLines !== 1) {
            $phpcsFile->addError("Expected 1 blank line before member var; $foundLines found", $stackPtr);
        }
    }

    /**
     * Processes normal variables
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file where this token was found
     * @param  integer              $stackPtr  The position where the token was found
     * @return void
     */
    public function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We don't care about normal variables
        $phpcsFile = 0;
        $stackPtr  = 0;
        return;
    }

    /**
     * Processes variables in double quoted strings
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file where this token was found
     * @param  integer              $stackPtr  The position where the token was found
     * @return void
     */
    public function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We don't care about normal variables
        $phpcsFile = 0;
        $stackPtr  = 0;
        return;
    }
}
