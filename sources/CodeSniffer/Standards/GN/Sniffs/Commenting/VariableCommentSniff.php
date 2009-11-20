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

if (class_exists('PHP_CodeSniffer_CommentParser_MemberCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_MemberCommentParser not found');
}

/**
 * GN_Sniffs_Commenting_VariableCommentSniff
 * Stripped version of Zend_Sniffs_Commenting_VariableCommentSniff
 *
 * Parses and verifies the variable doc comment
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GN_Sniffs_Commenting_VariableCommentSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff implements SQLI_CodeSniffer_Sniff
{
    /**
     * The header comment parser for the current file
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser $commentParser
     */
    public $commentParser = null;

    /**
     * Fix for spacing
     *
     * @var integer $space
     */
    public $space = 1;

    /**
     * Called to process class member vars
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned
     * @param  integer              $stackPtr  The position of the current token in the stack passed in $tokens
     * @return void
     */
    public function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;
        $tokens            = $phpcsFile->getTokens();
        $commentToken      = array(
                              T_COMMENT,
                              T_DOC_COMMENT,
                             );

        // Extract the var comment docblock
        $commentEnd = $phpcsFile->findPrevious($commentToken, ($stackPtr - 3));
        if ($commentEnd !== false and $tokens[$commentEnd]['code'] === T_COMMENT) {
		    $phpcsFile->addEvent(
		       'STYLE_VARIABLE_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
            return;
        } else if ($commentEnd === false or $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
		    $phpcsFile->addEvent(
		       'MISSING_VARIABLE_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
            return;
        } else {
            // Make sure the comment we have found belongs to us
            $commentFor = $phpcsFile->findNext(array(T_VARIABLE, T_CLASS, T_INTERFACE), ($commentEnd + 1));
            if ($commentFor !== $stackPtr) {
			    $phpcsFile->addEvent(
			       'MISSING_VARIABLE_COMMENT', 
			       array(),
			       $stackPtr
			    );        	
                return;
            }
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $comment      = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        // Parse the header comment docblock
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_MemberCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
		    $phpcsFile->addEvent(
		       'ERROR_PARSING_VARIABLE_COMMENT', 
		       array(),
		       $line
		    );        	
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
		    $phpcsFile->addEvent(
		       'VARIABLE_COMMENT_EMPTY', 
		       array(),
		       $commentStart
		    );        	
            return;
        }

        // Check for a comment description
        $short = $comment->getShortComment();
        if (trim($short) === '') {
		    $phpcsFile->addEvent(
		       'MISSING_SHORT_DESC_VARIABLE_COMMENT', 
		       array(),
		       $commentStart
		    );        	
		    return;
        } else {
            // No extra newline before short description
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' and $newlineSpan > 0) {
			    $phpcsFile->addEvent(
			       'EXTRA_LINE_VARIABLE_COMMENT', 
			       array('line' => $line),
			       ($commentStart + 1)
			    );        	
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
                if ($newlineBetween !== 2) {
				    $phpcsFile->addEvent(
				       'BLANK_LINE_BETWEEN_VARIABLE_COMMENT', 
				       array(),
				       ($commentStart + $newlineCount + 1)
				    );        	
                }

            }

            // Short description must be single line and end with a full stop
            $testShort = trim($short);
            $lastChar  = $testShort[(strlen($testShort) - 1)];
            if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
                $phpcsFile->addEvent(
				    'SHORT_DESC_VARIABLE_COMMENT', 
				    array(),
				    ($commentStart + 1)
				);        	
            }

        }

        // Exactly one blank line before tags
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                if (isset($long) and ($long !== '')) {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                if (isset($newlineCount) === false) {
	                $phpcsFile->addEvent(
					    'BLANK_LINE_BEFORE_VARIABLE_COMMENT', 
					    array(),
					    $commentStart
					);        	
                } else {
	                $phpcsFile->addEvent(
					    'BLANK_LINE_BEFORE_VARIABLE_COMMENT', 
					    array(),
					    ($commentStart + $newlineCount)
					);        	
                }

                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Check for unknown/deprecated tags
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            // Unknown tags are not parsed, do not process further
	        $phpcsFile->addWarning(
			    'BLANK_LINE_BEFORE_VARIABLE_COMMENT', 
			    array('tagname' => $errorTag[tag]),
			    ($commentStart + $errorTag['line'])
			);        	
        }

        // Check each tag
        $this->processVar($commentStart, $commentEnd);

    }

    /**
     * Process the var tag
     *
     * @param  integer $commentStart The position in the stack where the comment started
     * @param  integer $commentEnd   The position in the stack where the comment ended
     * @return void
     */
    public function processVar($commentStart, $commentEnd)
    {
        $var = $this->commentParser->getVar();

        if ($var !== null) {
            $errorPos = ($commentStart + $var->getLine());
            $index    = array_keys($this->commentParser->getTagOrders(), 'var');

            if (count($index) > 1) {
	            $this->currentFile->addEvent(
				   'ONE_VERSION_TAG_VARIABLE_COMMENT', 
				   array(),
				   $errorPos
				);        	
                return;
            }

            if ($index[0] !== 1) {
	            $this->currentFile->addEvent(
				   'ONE_VERSION_TAG_VARIABLE_COMMENT', 
				   array(),
				   $errorPos
				);        	
            }

            $content = $var->getContent();
            if (empty($content) === true) {
	            $this->currentFile->addEvent(
				   'TYPE_MISSING_VAR_TAG_VARIABLE_COMMENT', 
				   array(),
				   $errorPos
				);        	
                return;
            } else {
                $suggestedType = PHP_CodeSniffer::suggestType($content);
                if ($content !== $suggestedType) {
		            $this->currentFile->addEvent(
					   'EXPECTED_FOUND_VAR_TAG_VARIABLE_COMMENT', 
					   array('suggestedyype' => $suggestedType, 'content' => $content),
					   $errorPos
					);        	
                }
            }

            $spacing = substr_count($var->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== $this->space) {
		        $this->currentFile->addEvent(
				   'EXPECTED_SPACES_FOUND_VAR_TAG_VARIABLE_COMMENT', 
				   array('space' => $this->space, 'spacing' => $spacing),
				   $errorPos
				);        	
            }
        } else {
		    $this->currentFile->addEvent(
			   'MISSING_VAR_TAG_VARIABLE_COMMENT', 
			   array(),
			   $commentEnd
			);        	
        }

    }

    /**
     * Called to process a normal variable
     * Not required for this sniff
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found
     * @param  integer              $stackPtr  The position where the double quoted string was found
     * @return void
     */
    public function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile = 0;
        $stackPtr  = 0;
        return;
    }

    /**
     * Called to process variables found in duoble quoted strings
     * Not required for this sniff
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found
     * @param  integer              $stackPtr  The position where the double quoted string was found
     * @return void
     */
    public function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile = 0;
        $stackPtr  = 0;
        return;
    }
}
