<?php
/**
 * Parses and verifies the class doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id$
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
 * @package   SQLI_CodeSniffer
 * @author    David Choi <wdchoi@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
     * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
     * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
     * @version   Release: 1.1.0
     * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GNNew_Sniffs_Commenting_ClassCommentSniff implements PHP_CodeSniffer_Sniff
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
		    $error = 'Il faut utiliser "/**" comme style pour le commentaire de classe';
        	$phpcsFile->addError($error, $stackPtr, 'BadClassCommentStyle');        	    	
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
		    $phpcsFile->addError('Commentaire de classe manquant', $stackPtr, 'MissingClassComment');
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
                            $phpcsFile->addError('Commentaire de classe manquant', $stackPtr+1, 'MissingClassComment');      	                        	
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
                        $error = 'Il doit y avoir exactement deux lignes vides avant le commentaire de classe ('.$blankLineBefore.' ligne(s) trouvée(s))';
        				$phpcsFile->addError($error, $commentStart - 1, 'BlankLineClassComment');
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
            $phpcsFile->addError('Erreur de traitement commentaire de classe', $line, 'ErrorParsingClassComment');
			return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $phpcsFile->addError('Le commentaire de classe est vide', $commentStart, 'EmptyClassComment');
        	return;
        }

        // Check for a comment description.
        $short = rtrim($comment->getShortComment(), $phpcsFile->eolChar);
        if (trim($short) === '') {
            $phpcsFile->addError('La description courte dans le commentaire de classe est manquante', $commentStart, 'MissingShortDescClassComment');
        	return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $phpcsFile->addError($newlineSpan.' ligne(s) supplémentaire(s) trouvée(s) avant la description courte du commentaire de classe', $commentStart+1, 'ExtraLineClassComment');
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
            	$phpcsFile->addError('Il doit y avoir exactement 1 ligne entre les descriptions dans le commentaire de classe', $commentStart + $newlineCount + 1, 'BlankLineBetweenClassComment');
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
                $phpcsFile->addError('Il doit y avoir exactement une ligne avant une ligne de tag dans le commentaire de classe', $commentStart + $newlineCount, 'BlankLineBeforeParamClassComment');
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $testShort = trim($short);
        $lastChar  = $testShort[(strlen($testShort) - 1)];
        if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
            $phpcsFile->addError('La description courte doit être sur une seule ligne dans un commentaire de classe', $commentStart + 1, 'ShortDescClassComment');
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $tagname = $errorTag[tag];
    		$phpcsFile->addWarning($tagname.' est un tag non autorisé dans le commentaire de classe', $commentStart + $errorTag['line'], 'TagNotAllowedClassComment');
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
	            $this->currentFile->addWarning($tagName.' est un tag non autorisé dans le commentaire de classe', $commentEnd, 'TagNotAllowedClassComment');
                break;
            }
        }

        // category tag missing.
        if (in_array('category', $foundTags) === false) {
        	$this->currentFile->addError('Le tag @category est manquant dans le commentaire de classe', $commentEnd, 'MissingCategoryTagClassComment');
            return;
        }

        // package tag missing.
        if (in_array('package', $foundTags) === false) {
        	$this->currentFile->addError('Le tag @package est manquant dans le commentaire de classe', $commentEnd, 'MissingPackageTagClassComment');
	        return;
        }

        // subpackage tag missing.
        if (in_array('subpackage', $foundTags) === false) {
	        $this->currentFile->addError('Le tag @subpackage est manquant dans le commentaire de classe', $commentEnd, 'MissingSubpackageTagClassComment');
            return;
        }

        // author tag missing.
        if (in_array('author', $foundTags) === false) {
        	$this->currentFile->addError('Le tag @author est manquant dans le commentaire de classe', $commentEnd, 'MissingAuthorTagClassComment');
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
        	$this->currentFile->addError('Le tag @version est manquant dans le commentaire de classe', $commentEnd, 'MissingVersionTagClassComment');
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
        	$this->currentFile->addError('Un seul tag @version est autorisé dans le commentaire de classe', $errorPos, 'OneVersionTagClassComment');
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
            	$this->currentFile->addError('Tag @author vide dans le commentaire de la classe', $errorPos, 'MissingContentAuthorTagClassComment');
		    } else if (strpos($content,'$Author') !== 0) {
		    	$this->currentFile->addError('Parametre $Author attendu dans le tag @author', $errorPos, 'SubversionKeywordAuthorTagClassComment');
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
		    	$this->currentFile->addError('Tag @version vide dans le commentaire de la classe', $errorPos, 'MissingContentVersionTagClassComment');
            } else if (strpos($content,'$Revision') !== 0) {
                $this->currentFile->addError('Parametre $Revision attendu dans le tag @version', $errorPos, 'SubversionKeywordVersionTagClassComment');
		    }
        }
    }//end processVersion()
}//end class
?>
