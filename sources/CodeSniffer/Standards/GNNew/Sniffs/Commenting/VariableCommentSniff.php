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
 * GNNew_Sniffs_Commenting_VariableCommentSniff
 * Stripped version of Zend_Sniffs_Commenting_VariableCommentSniff
 *
 * Parses and verifies the variable doc comment
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GNNew_Sniffs_Commenting_VariableCommentSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff implements PHP_CodeSniffer_Sniff
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
		    $phpcsFile->addError('Il faut utiliser "/**" comme style pour le commentaire de variable' , $stackPtr, 'StyleVariableComment');
            return;
        } else if ($commentEnd === false or $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
		    $phpcsFile->addError('Commentaire de variable manquant' , $stackPtr, 'MissingVariableComment');
            return;
        } else {
            // Make sure the comment we have found belongs to us
            $commentFor = $phpcsFile->findNext(array(T_VARIABLE, T_CLASS, T_INTERFACE), ($commentEnd + 1));
            if ($commentFor !== $stackPtr) {
			    $phpcsFile->addError('Commentaire de variable manquant' , $stackPtr, 'MissingVariableComment');      	
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
		    $phpcsFile->addError('Erreur de traitement du commentaire de variable' , $line, 'ErrorParsingVariableComment');
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
        	$phpcsFile->addError('Commentaire de variable vide' , $commentStart, 'VariableCommentEmpty');
            return;
        }

        // Check for a comment description
        $short = $comment->getShortComment();
        if (trim($short) === '') {
		    $phpcsFile->addError('La description courte dans le commentaire de variable est manquante' , $commentStart, 'MissingShortDescVariableComment');
		    return;
        } else {
            // No extra newline before short description
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' and $newlineSpan > 0) {
			    $phpcsFile->addError($line.' ligne(s) supplémentaire(s) trouvé avant la description courte du commentaire de variable' , $commentStart + 1, 'ExtraLineVariableComment');
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
                if ($newlineBetween !== 2) {
				    $phpcsFile->addError('There must be exactly one blank line between descriptions in variable comment' , $commentStart + $newlineCount + 1, 'BlankLineBetweenVariableComment');
                }

            }

            // Short description must be single line and end with a full stop
            $testShort = trim($short);
            $lastChar  = $testShort[(strlen($testShort) - 1)];
            if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
                $phpcsFile->addError('La description courte doit être sur une seule ligne dans un commentaire de variable' , $commentStart + 1, 'ShortDescVariableComment');
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
                	$phpcsFile->addError('Il doit y avoir exactement une ligne avant une ligne de tag dans le commentaire de variable' , $commentStart, 'BlankLineBeforeVariableComment');
                } else {
                	$phpcsFile->addError('Il doit y avoir exactement une ligne avant une ligne de tag dans le commentaire de variable' , $commentStart + $newlineCount, 'BlankLineBeforeVariableComment');
                }

                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Check for unknown/deprecated tags
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            // Unknown tags are not parsed, do not process further
	        $phpcsFile->addWarning("Le tag @".$errorTag[tag]." est interdit dans le commentaire de variable" , $commentStart + $errorTag['line'], 'TagNotAllowedVariableComment');
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
	            $this->currentFile->addError("Le tag @version doit être présent une seule fois dans le commentaire de variable" , $errorPos, 'OneVersionTagVariableComment');
                return;
            }

            if ($index[0] !== 1) {
	            $this->currentFile->addError("Le tag @version doit être présent une seule fois dans le commentaire de variable" , $errorPos, 'OneVersionTagVariableComment');
            }

            $content = $var->getContent();
            if (empty($content) === true) {
            	$this->currentFile->addError("Le tag @type doit être suivi du type de variable" , $errorPos, 'TypeMissingVarTagVariableComment');
                return;
            } else {
                $suggestedType = PHP_CodeSniffer::suggestType($content);
                if ($content !== $suggestedType) {
		            $this->currentFile->addError('Le tag @type devrait être suivi de "'.$suggestedType.'", "'.$content.'" trouvé' , $errorPos, 'ExpectedFoundVarTagVariableComment');
                }
            }

            $spacing = substr_count($var->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== $this->space) {
		        $this->currentFile->addError($this->space.' espace(s) attendu(s), '.$spacing.' trouvé(s)' , $errorPos, 'ExpectedSpacesFoundVarTagVariableComment');
            }
        } else {
		    $this->currentFile->addError('Le tag @var est manquant dans le commentaire de variable' , $commentEnd, 'MissingVarTagVariableComment');
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
