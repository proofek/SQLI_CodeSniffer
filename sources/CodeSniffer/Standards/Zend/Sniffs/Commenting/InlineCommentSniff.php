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
 * Zend_CodeSniffer_Sniffs_PEAR_Commenting_InlineCommentSniff
 *
 * Checks that no perl-style comments are used
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_Commenting_InlineCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for
     *
     * @return array
     */
    public function register()
    {
        return array(T_COMMENT);
    }

    /**
     * Processes this test, when one of its tokens is encountered
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned
     * @param  integer              $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens      = $phpcsFile->getTokens();
        $topComment  = $stackPtr;
        $lastComment = $stackPtr;

        // If this is a function/class/interface doc block comment, skip it
        // We are only interested in inline doc block comments, which are
        // not allowed
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT) {
            $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $ignore    = array(T_CLASS,  T_INTERFACE, T_FUNCTION, T_PUBLIC, T_PRIVATE, T_PROTECTED,
                               T_STATIC, T_ABSTRACT,  T_CONST);
            if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
                return;
            } else {
                $prevToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1),
                                                      null, true);
                if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                    return;
                }

                // Only error once per comment
                if (substr($tokens[$stackPtr]['content'], 0, 3) === '/**') {
                    $error = 'Inline doc block comments are not allowed; use "/* Comment */" or "// Comment" instead';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
        }

        if ($tokens[$stackPtr]['content']{0} === '#') {
            $error = 'Perl-style comments are not allowed; use "// Comment" instead';
            $phpcsFile->addError($error, $stackPtr);
        }

        // We don't want end of block comments. If the last comment is a closing
        // curly brace
        $previousContent = $phpcsFile->findPrevious(array(T_WHITESPACE), ($stackPtr - 1), null, true);
        if (($tokens[$previousContent]['line'] === $tokens[$stackPtr]['line']) and
            ($tokens[$previousContent]['code'] === T_CLOSE_CURLY_BRACKET)) {
            return;
        }

        $comment = rtrim($tokens[$stackPtr]['content']);
        // Only want inline comments
        if (substr($comment, 0, 2) !== '//') {
            return;
        }

        $spaceCount = 0;
        $len        = strlen($comment);
        for ($i = 2; $i < $len; $i++) {
            if ($comment[$i] !== ' ') {
                break;
            }

            $spaceCount++;
        }

        if ($spaceCount === 0) {
            $error = 'No space before comment text; expected "// ' . substr($comment, 2)
                   . '" but found "' . $comment . '"';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($spaceCount > 1) {
            $error = $spaceCount . ' spaces found before inline comment; expected "// '
                   . substr($comment, (2 + $spaceCount)) . '" but found "' . $comment . '"';
            $phpcsFile->addError($error, $stackPtr);
        }

        // The below section determines if a comment block is correctly capitalised,
        // and ends in a full-stop. It will find the last comment in a block, and
        // work its way up
        $nextComment = $phpcsFile->findNext(array(T_COMMENT), ($stackPtr + 1), null, false);

        if (($nextComment !== false) and (($tokens[$nextComment]['line']) === ($tokens[$stackPtr]['line'] + 1))) {
            return;
        }

        $topComment = $phpcsFile->findPrevious(array(T_COMMENT), ($lastComment - 1), null, false);
        while ($topComment !== false) {
            if ($tokens[$topComment]['line'] !== ($tokens[$lastComment]['line'] - 1)) {
                break;
            }

            $lastComment = $topComment;
            $topComment  = $phpcsFile->findPrevious(array(T_COMMENT), ($lastComment - 1), null, false);
        }

        $topComment  = $lastComment;
        $commentText = '';

        for ($i = $topComment; $i <= $stackPtr; $i++) {
            if ($tokens[$i]['code'] === T_COMMENT) {
                $commentText .= trim(substr($tokens[$i]['content'], 2));
            }
        }

        if ($commentText === '') {
            $error = 'Blank comments are not allowed';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        if (preg_match('|[A-Z]|', $commentText[0]) === 0) {
            $error = 'Inline comments must start with a capital letter';
            $phpcsFile->addError($error, $topComment);
        }

        // Finally, the line below the last comment cannot be empty
        $start = false;
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['line'] === ($tokens[$stackPtr]['line'] + 1)) {
                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    return;
                }
            } else if ($tokens[$i]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
                break;
            }
        }

        $error = 'There must be no blank line following an inline comment';
        $phpcsFile->addError($error, $stackPtr);
    }
}
