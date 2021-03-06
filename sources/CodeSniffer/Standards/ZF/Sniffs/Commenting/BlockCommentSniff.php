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
 * ZF_Sniffs_Commenting_BlockCommentSniff
 *
 * Verifies that block comments are used appropriately
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZF_Sniffs_Commenting_BlockCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for
     *
     * @return array
     */
    public function register()
    {
        return array(T_COMMENT, T_DOC_COMMENT);
    }

    /**
     * Processes this test, when one of its tokens is encountered
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The current file being scanned
     * @param  integer              $stackPtr  The position of the current token in the stack passed in $tokens
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If its an inline comment return
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '/*') {
            return;
        }

        // If this is a function/class/interface doc block comment, skip it
        // We are only interested in inline doc block comments
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT) {
            $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $ignore    = array(
                               T_CLASS,
                               T_INTERFACE,
                               T_FUNCTION,
                               T_PUBLIC,
                               T_PRIVATE,
                               T_PROTECTED,
                               T_STATIC,
                               T_ABSTRACT,
                               T_CONST,
                         );
            if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
                return;
            }

            $prevToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                return;
            }
        }

        $commentLines = array($stackPtr);
        $nextComment  = $stackPtr;
        $lastLine     = $tokens[$stackPtr]['line'];

        // Construct the comment into an array
        $nextComment = $phpcsFile->findNext($tokens[$stackPtr]['code'], ($nextComment + 1), null, false);
        while ($nextComment !== false) {
            if (($tokens[$nextComment]['line'] - 1) !== $lastLine) {
                // Not part of the block.
                break;
            }

            $lastLine       = $tokens[$nextComment]['line'];
            $commentLines[] = $nextComment;
            $nextComment    = $phpcsFile->findNext($tokens[$stackPtr]['code'], ($nextComment + 1), null, false);
        }

        if (count($commentLines) <= 2) {
            // Small comment. Can't be right
            if (count($commentLines) === 1) {
                $error = 'Single line block comment not allowed; use inline ("// text") comment instead';
                $phpcsFile->addError($error, $stackPtr, 'SingleLineBlockCommentNotAllowed');
                return;
            }

            //TODO A 3 lines block comment, triggers a 'Empty line not allowed at start of comment' error instead of 'Empty block comment not allowed'
            if (trim($tokens[$commentLines[1]]['content']) === '*/') {
                if (trim($tokens[$stackPtr]['content']) === '/*') {
                    $error = 'Empty block comment not allowed';
                    $phpcsFile->addError($error, $stackPtr, 'EmptyBlockCommentNotAllowed');
                    return;
                }
            }
        }

        $content = trim($tokens[$stackPtr]['content']);
        if ($content !== '/*' and $content !== '/**') {
            $error = 'Block comment text must start on a new line';
            $phpcsFile->addError($error, $stackPtr, 'BlockCommentOnNewLine');
            return;
        }

        $starColumn = $tokens[$stackPtr]['column'];		
        // Make sure first line isn't blank
        if (in_array(trim($tokens[$commentLines[1]]['content']), array('', '*'))) {
            $error = 'Empty line not allowed at start of comment';
            $phpcsFile->addError($error, $commentLines[1], 'EmptyLineStartBlockCommentNotAllowed');
        } else {
            // Check indentation of first line
            $content      = $tokens[$commentLines[1]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== $starColumn) {
                $expected  = $starColumn;
                $expected .= ($starColumn === 1) ? ' space' : ' spaces';
                $error     = "First line of comment not aligned correctly; expected $expected but found $leadingSpace";
                $phpcsFile->addError($error, $commentLines[1], '????????????????????????');
            }
            
            if (($commentText[0] !== '*') or ($commentText[1] != ' ')) {
                $error = "Block comments must start with a '* ' seperation";
                $phpcsFile->addError($error, $commentLines[1], '????????????????????????');
            }

            if (preg_match('|[A-Z]|', trim(strtr($commentText, array('*/', '  ')))) === 0) {
                $error = 'Block comments must start with a capital letter';
                $phpcsFile->addError($error, $commentLines[1], 'BlockCommentStartsWithCapitalLetter');
            }
        }

        // Check that each line of the comment is indented past the star
        foreach ($commentLines as $line) {
            $leadingSpace = (strlen($tokens[$line]['content']) - strlen(ltrim($tokens[$line]['content'])));
            // First and last lines (comment opener and closer) are handled seperately.
            if (($line === $commentLines[(count($commentLines) - 1)]) or ($line === $commentLines[0])) {
                continue;
            }

            // First comment line was handled above
            if ($line === $commentLines[1]) {
                continue;
            }

            // If it's empty, continue
            if (trim($tokens[$line]['content']) === '') {
                continue;
            }

            if ($leadingSpace < $starColumn) {
                $expected  = $starColumn;
                $expected .= ($starColumn === 1) ? ' space' : ' spaces';
                $error     = "Comment line indented incorrectly; expected at least $expected but found $leadingSpace";
                $phpcsFile->addError($error, $line, 'CommentLineIndentedIncorrectly');
            }

            $tmp = trim($tokens[$line]['content']);
            if ($tmp[0] !== '*' or ((strlen($tmp) > 1) and ($tmp[1] !== ' '))) {
                $error = "Block comments must start with a '* ' seperation";
                $phpcsFile->addError($error, $commentLines[1], '????????????????????????');
            }
        }

        // Finally, test the last line is correct
        $lastIndex = (count($commentLines) - 1);
        $content   = trim($tokens[$commentLines[$lastIndex]]['content']);
        if ($content !== '*/' and $content !== '**/') {
            $error = 'Comment closer must be on a new line';
            $phpcsFile->addError($error, $commentLines[$lastIndex], 'BlockCommentCloserOnNewLine');
        } else {
            $content      = $tokens[$commentLines[$lastIndex]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== ($tokens[$stackPtr]['column'])) {
                $expected  = ($tokens[$stackPtr]['column']);
                $expected .= ($expected === 1) ? ' space' : ' spaces';
                $error     = "Last line of comment aligned incorrectly; expected $expected but found $leadingSpace";
                $phpcsFile->addError($error, $commentLines[$lastIndex], 'LastCommentLineIndentation');
            }
        }

        // Check that the lines before and after this comment are blank
        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);        
        if ($tokens[$contentBefore]['code'] === T_OPEN_CURLY_BRACKET) {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) >1) {
                $error = 'Empty line not required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'NoEmptyLineBeforeBlockComment');
            }
        } else {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) < 2) {
                $error = 'Empty line required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'EmptyLineRequiredBeforeBlockComment');
            }
        }

        $commentCloser = $commentLines[$lastIndex];
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($commentCloser + 1), null, true);
        if (($tokens[$contentAfter]['line'] - $tokens[$commentCloser]['line']) > 1) {
            $error = 'Empty line after block comment not allowed';
            $phpcsFile->addError($error, $commentCloser, 'NoEmptyLineAfterBlockComment');
        }
    }
}
