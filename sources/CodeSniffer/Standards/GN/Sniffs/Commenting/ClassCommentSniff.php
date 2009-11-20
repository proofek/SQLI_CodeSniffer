<?php
/**
 * Parses and verifies the class doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ClassCommentSniff.php,v 1.18 2007/11/04 22:29:51 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the class doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A class doc comment exists.</li>
 *  <li>There is exactly one blank line before the class comment.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>Each paragraph of the long description ends with a full stop.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Il y a les tags author et version</li>
 *  <li>Check le contenu du tag author.</li>
 *  <li>Check le format du tag version (x.x.x).</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
     * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
     * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
     * @version   Release: 1.1.0
     * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GN_Sniffs_Commenting_ClassCommentSniff implements SQLI_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $find   = array (
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
		    $phpcsFile->addEvent(
		       'STYLE_CLASS_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
		    $phpcsFile->addEvent(
		       'MISSING_CLASS_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $stackPtr, false, $phpcsFile->eolChar);
                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $phpcsFile->addEvent(
						       'MISSING_CLASS_COMMENT', 
						       array(),
						       $stackPtr + 1
						    );        	                        	
                            return;
                        }
                    }//end if
                }//end if

                // Exactly one blank line before the class comment.
                $prevTokenEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($commentStart - 1), null, true);
                if ($prevTokenEnd !== false) {
                    $blankLineBefore = 0;
                    for ($i = ($prevTokenEnd + 1); $i < $commentStart; $i++) {
                        if ($tokens[$i]['code'] === T_WHITESPACE && $tokens[$i]['content'] === $phpcsFile->eolChar) {
                            $blankLineBefore++;
                        }
                    }

                    if ($blankLineBefore !== 2) {
                        $phpcsFile->addEvent(
						   'BLANK_LINE_CLASS_COMMENT', 
						   array(),
						   $commentStart - 1
						);        	                        	
                    }
                }

            }//end if
        }//end if

        $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        // Parse the class comment docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addEvent(
			    'ERROR_PARSING_CLASS_COMMENT', 
				array(),
				$line
			);        	                        	            
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $phpcsFile->addEvent(
			    'CLASS_COMMENT_EMPTY', 
				array(),
				$commentStart
			);        	                        	
            return;
        }

        // Check for a comment description.
        $short = rtrim($comment->getShortComment(), $phpcsFile->eolChar);
        if (trim($short) === '') {
            $phpcsFile->addEvent(
			    'MISSING_SHORT_DESC_CLASS_COMMENT', 
				array(),
				$commentStart
			);        	                        	
            return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $phpcsFile->addEvent(
			    'EXTRA_LINE_CLASS_COMMENT', 
				array('line' => $line),
				$commentStart + 1
			);        	                        	            
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
	            $phpcsFile->addEvent(
				    'BLANK_LINE_BETWEEN_CLASS_COMMENT', 
					array(),
					($commentStart + $newlineCount + 1)
				);        	                        	            
            }
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addEvent(
				    'BLANK_LINE_BEFORE_PARAM_CLASS_COMMENT', 
					array(),
					($commentStart + $newlineCount)
				);        	                        	            
                
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $testShort = trim($short);
        $lastChar  = $testShort[(strlen($testShort) - 1)];
        if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
            $phpcsFile->addEvent(
			    'SHORT_DESC_CLASS_COMMENT', 
				array(),
				($commentStart + 1)
			);        	                        	            
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $tagname = $errorTag[tag];
            $phpcsFile->addEvent(
			    'TAG_NOTALLOWED_CLASS_COMMENT', 
				array('tagname' => $tagname),
				($commentStart + $errorTag['line'])
			);        	                        	            
            return;
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        $foundTags = $this->commentParser->getTagOrders();

        // Other tags found.
        foreach ($foundTags as $tagName) {
            if (!in_array($tagName, array('comment', 'author', 'version', 'category', 'package', 'subpackage'))) {
                $tagname . ' tag not allowed in class comment';
	            $this->currentFile->addEvent(
				    'TAG_NOTALLOWED_CLASS_COMMENT', 
					array('tagname' => $tagname),
					$commentEnd
				);        	                        	            
                break;
            }
        }

        // category tag missing.
        if (in_array('category', $foundTags) === false) {
	        $this->currentFile->addEvent(
			   'MISSING_CATEGORY_TAG_CLASS_COMMENT', 
				array(),
				$commentEnd
			);        	                        	            
            return;
        }

        // package tag missing.
        if (in_array('package', $foundTags) === false) {
	        $this->currentFile->addEvent(
			   'MISSING_PACKAGE_TAG_CLASS_COMMENT', 
				array(),
				$commentEnd
			);        	                        	            
            return;
        }

        // subpackage tag missing.
        if (in_array('subpackage', $foundTags) === false) {
	        $this->currentFile->addEvent(
			   'MISSING_SUBPACKAGE_TAG_CLASS_COMMENT', 
				array(),
				$commentEnd
			);        	                        	            
            return;
        }

        // author tag missing.
        if (in_array('author', $foundTags) === false) {
	        $this->currentFile->addEvent(
			   'MISSING_AUTHOR_TAG_CLASS_COMMENT', 
				array(),
				$commentEnd
			);        	                        	            
            return;
        }

        // Get the line number for current tag.
        $authors = $this->commentParser->getAuthors();
        if (is_null($authors) === true || empty($authors) === true) {
            return;
        }

        foreach ($authors as $author) {
            $errorPos = ($commentStart + $author->getLine());
// SQLI : mieux vaut aligner les valeurs
//            // Check spacing.
//            if ($author->getContent() !== '') {
//                $spacing = substr_count($author->getWhitespaceBeforeContent(), ' ');
//                if ($spacing !== 1) {
//                    $error = "Expected 1 space but found $spacing before author name in @author tag";
//                    $this->currentFile->addError($error, $errorPos);
//                }
//            }
    
            // Check content.
            $this->processAuthor($author, $errorPos);
        }
        
        // version tag missing.
        if (in_array('version', $foundTags) === false) {
	        $this->currentFile->addEvent(
			   'MISSING_VERSION_TAG_CLASS_COMMENT', 
				array(),
				$commentEnd
			);        	                        	            
            return;
        }

        // Get the line number for current tag.
        $version = $this->commentParser->getVersion();
        if (is_null($version) === true || empty($version) === true) {
            return;
        }

        $errorPos = ($commentStart + $version->getLine());

        // Make sure there is no duplicate tag.
        $foundIndexes = array_keys($foundTags, 'version');
        if (count($foundIndexes) > 1) {
	        $this->currentFile->addEvent(
			   'ONE_VERSION_TAG_CLASS_COMMENT', 
				array(),
				$errorPos
			);        	                        	            
        }
// SQLI : mieux vaut aligner les valeurs
//        // Check spacing.
//        if ($version->getContent() !== '') {
//            $spacing = substr_count($version->getWhitespaceBeforeContent(), ' ');
//            if ($spacing !== 1) {
//                $error = "Expected 1 space but found $spacing before version number in @version tag";
//                $this->currentFile->addError($error, $errorPos);
//            }
//        }

        // Check content.
        $this->processVersion($errorPos);

    }//end processTags()


    /**
     * Processes the author tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processAuthor($author, $errorPos)
    {
        if ($author !== null) {
            $content = $author->getContent();
            if (empty($content) === true) {
		        $this->currentFile->addEvent(
				   'MISSING_CONTENT_AUTHOR_TAG_CLASS_COMMENT', 
					array(),
					$errorPos
				);        	                        	            
            } else if (strpos($content,'$Author') !== 0) {
		        $this->currentFile->addEvent(
				   'SUBVERSION_KEYWORD_AUTHOR_TAG_CLASS_COMMENT', 
					array(),
					$errorPos
				);        	                        	            
            }
        }
    }//end processAuthor()


    /**
     * Processes the version tag.
     *
     * The version tag must have the exact keyword '$Revision: $'
     * or is in the form x.x.x
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @version tag in class comment';
		        $this->currentFile->addEvent(
				   'MISSING_CONTENT_VERSION_TAG_CLASS_COMMENT', 
					array(),
					$errorPos
				);        	                        	            
            } else if (strpos($content,'$Revision') !== 0) {
                $error = 'Expected subversion $Revision keyword in @version tag';
		        $this->currentFile->addEvent(
				   'SUBVERSION_KEYWORD_VERSION_TAG_CLASS_COMMENT', 
					array(),
					$errorPos
				);        	                        	            
            }
        }
    }//end processVersion()
}//end class
?>
