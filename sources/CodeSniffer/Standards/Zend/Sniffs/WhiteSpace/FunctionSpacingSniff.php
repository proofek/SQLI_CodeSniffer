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
 * Zend_Sniffs_WhiteSpace_FunctionSpacingSniff
 *
 * Checks the separation between methods in a class or interface
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_WhiteSpace_FunctionSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION
               );
    }

    /**
     * Processes this sniff, when one of its tokens is encountered
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned
     * @param  integer              $stackPtr  The position of the current token in the stack passed in $tokens
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check the number of blank lines after the function
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Must be an interface method, so the closer is the semi-colon.
            $closer = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        } else {
            $closer = $tokens[$stackPtr]['scope_closer'];
        }

        // There needs to be 1 blank line after the closer
        $nextLineToken = null;
        for ($i = $closer; $i < $phpcsFile->numTokens; $i++) {
            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                continue;
            } else {
                $nextLineToken = ($i + 1);
                break;
            }
        }

        if (is_null($nextLineToken) === true) {
            // Never found the next line, which means there are 0 blank lines after the function
            $foundLines = 0;
        } else {
            $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), ($nextLineToken + 1), null, true);
            if ($nextContent === false) {
                // We are at the end of the file.
                $foundLines = 0;
            } else {
                $foundLines = ($tokens[$nextContent]['line'] - $tokens[$nextLineToken]['line']);
            }
        }

        if ((array_key_exists(($closer + 2), $tokens) and ($tokens[($closer + 2)]['content'] === '}')) or
            (array_key_exists(($closer + 3), $tokens) and ($tokens[($closer + 3)]['content'] === '}')) or
            (array_key_exists(($closer + 4), $tokens) and ($tokens[($closer + 4)]['content'] === '}'))) {
            if ($tokens[($closer + 2)]['content'] !== '}' ) {
                $phpcsFile->addError("Expected 0 blank lines after last function; $foundLines found", ($closer + 1));
            }
        } else if ($foundLines !== 1) {
            $phpcsFile->addError("Expected 1 blank lines after function; $foundLines found", $closer);
        }

        // Check the number of blank lines before the function
        $prevLineToken = null;
        for ($i = $stackPtr; $i > 0; $i--) {
            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                continue;
            } else {
                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means there are 0 blank lines before the function
            $foundLines = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(array(T_WHITESPACE, T_DOC_COMMENT), $prevLineToken, null, true);

            // Before we throw an error, check that we are not throwing an error
            // for another function. We don't want to error for no blank lines after
            // the previous function and no blank lines before this one as well
            $currentLine = $tokens[$stackPtr]['line'];
            $prevLine    = ($tokens[$prevContent]['line'] - 1);
            $i           = ($stackPtr - 1);
            $foundLines  = 0;
            while ($currentLine != $prevLine and $currentLine > 1 and $i > 0) {
                if (isset($tokens[$i]['scope_condition']) === true) {
                    $scopeCondition = $tokens[$i]['scope_condition'];
                    if ($tokens[$scopeCondition]['code'] === T_FUNCTION) {
                        // Found a previous function.
                        return;
                    }
                } else if ($tokens[$i]['code'] === T_FUNCTION) {
                    // Found another interface function
                    return;
                }

                $currentLine = $tokens[$i]['line'];
                if ($currentLine === $prevLine) {
                    break;
                }

                if ($tokens[($i - 1)]['line'] < $currentLine and $tokens[($i + 1)]['line'] > $currentLine) {
                    // This token is on a line by itself. If it is whitespace, the line is empty
                    if ($tokens[$i]['code'] === T_WHITESPACE) {
                        $foundLines++;
                    }
                }

                $i--;
            }
        }

        if ($tokens[$prevContent]['content'] === '{') {
            if ($foundLines !== 0) {
                $phpcsFile->addError("Expected 0 blank lines before function; $foundLines found", $stackPtr);
            }
        } else if ($foundLines !== 1) {
            $phpcsFile->addError("Expected 1 blank line before function; $foundLines found", $stackPtr);
        }
    }
}
