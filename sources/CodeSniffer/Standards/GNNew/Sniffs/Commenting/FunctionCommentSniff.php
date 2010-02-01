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
if (class_exists('PHP_CodeSniffer_CommentParser_FunctionCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_FunctionCommentParser not found');
}

/**
 * GNNew_Sniffs_Commenting_FunctionCommentSniff
 * Stripped version of Zend_Sniffs_Commenting_FunctionCommentSniff
 *
 * Parses and verifies the doc comments for functions
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GNNew_Sniffs_Commenting_FunctionCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * The name of the method that we are currently processing
     *
     * @var string
     */
    private $_methodName = '';

    /**
     * The position in the stack where the function token was found
     *
     * @var integer
     */
    private $_functionToken = null;

    /**
     * The position in the stack where the class token was found
     *
     * @var integer
     */
    private $_classToken = null;

    /**
     * The index of the current tag we are processing
     *
     * @var integer
     */
    private $_tagIndex = 0;

    /**
     * The found tokens
     *
     * @var array
     */
    private $_tokens = null;

    /**
     * The function comment parser for the current method
     *
     * @var PHP_CodeSniffer_Comment_Parser_FunctionCommentParser
     */
    protected $_commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing
     *
     * @var PHP_CodeSniffer_File
     */
    protected $_currentFile = null;

    /**
     * Returns an array of tokens this test wants to listen for
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }

    /**
     * Processes this test, when one of its tokens is encountered
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned
     * @param  integer              $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_currentFile = $phpcsFile;
        $this->_tokens      = $phpcsFile->getTokens();

        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1));

        if ($commentEnd === false) {
            return;
        }

        // If the token that we found was a class or a function, then this
        // function has no doc comment
        $code = $this->_tokens[$commentEnd]['code'];

        if ($code === T_COMMENT) {
        	$phpcsFile->addError('Il faut utiliser "/**" comme style pour le commentaire de fonction' , $stackPtr, 'StyleFunctionComment');
            return;
        } else if ($code !== T_DOC_COMMENT) {
        	$phpcsFile->addError('Commentaire de fonction manquant' , $stackPtr, 'MissingFunctionComment');
            return;
        }

        // If there is any code between the function keyword and the doc block
        // then the doc block is not for us
        $ignore    = PHP_CodeSniffer_Tokens::$scopeModifiers;
        $ignore[]  = T_STATIC;
        $ignore[]  = T_WHITESPACE;
        $ignore[]  = T_ABSTRACT;
        $ignore[]  = T_FINAL;
        $prevToken = $phpcsFile->findPrevious($ignore, ($stackPtr - 1), null, true);
        if ($prevToken !== $commentEnd) {
		    $phpcsFile->addError('Il faut utiliser "/**" comme style pour le commentaire de fonction' , $stackPtr, 'StyleFunctionComment');        	
            return;
        }

        $this->_functionToken = $stackPtr;

        foreach ($this->_tokens[$stackPtr]['conditions'] as $condPtr => $condition) {
            if ($condition === T_CLASS or $condition === T_INTERFACE) {
                $this->_classToken = $condPtr;
                break;
            }
        }

        // Find the first doc comment
        $commentStart      = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $comment           = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));
        $this->_methodName = $phpcsFile->getDeclarationName($stackPtr);

        try {
            $this->_commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment, $phpcsFile);
            $this->_commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
		    $phpcsFile->addError('Erreur de traitement commentaire de fonction' , $line, 'ErrorParsingFunctionComment');
            return;
        }

        $comment = $this->_commentParser->getComment();
        if (is_null($comment) === true) {
		    $phpcsFile->addError('Le commentaire de fonction est vide' , $commentStart, 'FunctionCommentEmpty');
            return;
        }

        $tagOrder = $this->_commentParser->getTagOrders();
        $cnt      = -1;
        $con      = 'comment';
        foreach ($tagOrder as $tag => $content) {
            switch ($content) {
                case 'comment':
                    $cnt = $tag;
                    break;

                case 'param':
                    if (($con !== 'comment') and ($con !== 'param')) {
					    $this->_currentFile->addError('Le tag @param doit suivre le commentaire de la fonction' , $commentStart + $tag, 'ParamTagOrderFunctionComment');
                    }
                    break;

                case 'return':
                    if (($con !== 'comment') and ($con !== 'param')) {
					    $this->_currentFile->addError('Le tag @param doit suivre le commentaire de la fonction' , $commentStart + $tag, 'ParamTagOrderFunctionComment');
                    }
                    break;

                default:
                		$this->_currentFile->addWarning("Le tag @$content est interdit" , $commentStart + $tag, 'TagNotAllowedFunctionComment');
                    break;
            }

            $con = $content;
        }

        $this->_processParams($commentStart, $commentEnd);
        $this->_processReturn($commentStart, $commentEnd);

        // Check for a comment description
        $short = $comment->getShortComment();
        if (trim($short) === '') {
		    $phpcsFile->addError("La description courte dans le commentaire de fonction manquante" , $commentStart, 'MissingShortDescFunctionComment');
            return;
        }

        // No extra newline before short description
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' and $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
		    $phpcsFile->addError("$line ligne(s) trouvée(s) en plus avant le commentaire court de fonction" , $commentStart + 1, 'ExtraLineFunctionComment');
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = '';
			    $phpcsFile->addError("Il doit y avoir 1 ligne vide après la description courte dans le commentaire de fonction" , $commentStart + $newlineCount + 1, 'BlankLineFunctionComment');
            }

        }

        // Exactly one blank line before tags
        $params = $this->_commentParser->getTagOrders();
        if (count($params) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }
				$phpcsFile->addError("Il doit y avoir 1 ligne vide avant les tags dans le commentaire de fonction" , $commentStart + $newlineCount, 'BlankLineTagsFunctionComment');
                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Check for unknown/deprecated tags
        $unknownTags = $this->_commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $tagname = $errorTag['tag'];
			$phpcsFile->addWarning("Le tag @$tagname est interdit" , $commentStart + $errorTag['line'], 'TagNotAllowedFunctionComment');
        }

    }

    /**
     * Process the return comment of this function comment
     *
     * @param  integer $commentStart The position in the stack where the comment started
     * @param  integer $commentEnd   The position in the stack where the comment ended
     * @return void
     */
    protected function _processReturn($commentStart, $commentEnd)
    {
        // Skip constructor and destructor
        $className = '';
        if ($this->_classToken !== null) {
            $className = $this->_currentFile->getDeclarationName($this->_classToken);
            $className = strtolower(ltrim($className, '_'));
        }

        $methodName      = strtolower(ltrim($this->_methodName, '_'));
        $isSpecialMethod = ($this->_methodName === '__construct' or $this->_methodName === '__destruct');
        $return          = $this->_commentParser->getReturn();

        if ($methodName === $className or $isSpecialMethod !== false) {
            // No return tag for constructor and destructor
            if ($return !== null) {
                $errorPos = ($commentStart + $return->getLine());
				$this->_currentFile->addError("Le tag @return n'est pas requis dans le constructeur et le destructeur" , $errorPos, 'TagReturnNotRequiredFunctionComment');
            }
            return;
        }

        if ($return === null) {
			$this->_currentFile->addError("Le tag @return est manquant dans le commentaire de fonction" , $commentEnd, 'TagReturnMissingFunctionComment');
            return;
        }

        $tagOrder = $this->_commentParser->getTagOrders();
        $index    = array_keys($tagOrder, 'return');
        $errorPos = ($commentStart + $return->getLine());
        $content  = trim($return->getRawContent());

        if (count($index) > 1) {
			$this->_currentFile->addError("Une seule version du tag @return est admise dans un commentaire de fonction" , $errorPos, 'OneVersionTagFunctionComment');
            return;
        }

//        $since = array_keys($tagOrder, 'since');
//        if (count($since) === 1 and $this->_tagIndex !== 0) {
//            $this->_tagIndex++;
//            if ($index[0] !== $this->_tagIndex) {
//                $error = 'The @return tag is in the wrong order; the tag follows @see (if used) or @since';
//                $this->_currentFile->addError($error, $errorPos);
//            }
//        }

        if (empty($content) === true) {
        	$this->_currentFile->addError("Type manquant dans le tag @return dans le commentaire de fonction" , $errorPos, 'TypeMissingTagReturnFunctionComment');
        } else {
            // Check return type (can be multiple, separated by '|')
            $typeNames      = explode('|', $content);
            $suggestedNames = array();
            foreach ($typeNames as $i => $typeName) {
                $suggestedName = PHP_CodeSniffer::suggestType($typeName);
                if (in_array($suggestedName, $suggestedNames) === false) {
                    $suggestedNames[] = $suggestedName;
                }
            }

            $suggestedType = implode('|', $suggestedNames);
            if ($content !== $suggestedType) {
				$this->_currentFile->addError("Function return type $content is invalid" , $errorPos, 'TypeReturnInvalidFunctionComment');
            }

            $tokens = $this->_currentFile->getTokens();

            // If the return type is void, make sure there is
            // no return statement in the function
            if ($content === 'void') {
                if (isset($tokens[$this->_functionToken]['scope_closer']) === true) {
                    $endToken = $tokens[$this->_functionToken]['scope_closer'];
                    $return   = $this->_currentFile->findNext(T_RETURN, $this->_functionToken, $endToken);
                    if ($return !== false) {
                        // If the function is not returning anything, just
                        // exiting, then there is no problem
                        $semicolon = $this->_currentFile->findNext(T_WHITESPACE, ($return + 1), null, true);
                        if ($tokens[$semicolon]['code'] !== T_SEMICOLON) {
							$this->_currentFile->addError("Function return type is void, but function contains return statement" , $errorPos, 'TypeReturnVoidFunctionComment');
                        }
                    }
                }
            } else {
                // If return type is not void, there needs to be a
                // returns statement somewhere in the function that
                // returns something
                if (isset($tokens[$this->_functionToken]['scope_closer']) === true) {
                    $endToken = $tokens[$this->_functionToken]['scope_closer'];
                    $return   = $this->_currentFile->findNext(T_RETURN, $this->_functionToken, $endToken);
                    if ($return === false) {
						$this->_currentFile->addError("Function return type is not void, but function has no return statement" , $errorPos, 'TypeReturnNotVoidNoStatementFunctionComment');
                    } else {
                        $semicolon = $this->_currentFile->findNext(T_WHITESPACE, ($return + 1), null, true);
                        if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
							$this->_currentFile->addError("Function return type is not void, but function has no return statement" , $errorPos, 'TypeReturnNotVoidFunctionComment');
                        }
                    }
                }
            }
        }
    }

    /**
     * Process the function parameter comments
     *
     * @param  integer $commentStart The position in the stack where the comment started
     * @param  integer $commentEnd   The position in the stack where the comment ended
     * @return void
     */
    protected function _processParams($commentStart, $commentEnd)
    {
        $realParams  = $this->_currentFile->getMethodParameters($this->_functionToken);
        $params      = $this->_commentParser->getParams();
        $foundParams = array();

        if (empty($params) === false) {
            $isSpecialMethod = ($this->_methodName === '__construct' or $this->_methodName === '__destruct');
            if ((substr_count($params[(count($params) - 1)]->getWhitespaceAfter(),
                              $this->_currentFile->eolChar) !== 1) and ($isSpecialMethod === false)) {
                $errorPos = ($params[(count($params) - 1)]->getLine() + $commentStart);
				$this->_currentFile->addError("No empty line after last parameter comment allowed" , $errorPos + 1, 'EmptyLineLastParameterFunctionComment');
            }

            // Parameters must appear immediately after the comment
            if ($params[0]->getOrder() !== 2) {
                $errorPos = ($params[0]->getLine() + $commentStart);
				$this->_currentFile->addError("Parameters must appear immediately after the comment" , $errorPos, 'ParameterAfterCommentFunctionComment');
            }

            $previousParam      = null;
            $spaceBeforeVar     = 10000;
            $spaceBeforeComment = 10000;
            $longestType        = 0;
            $longestVar         = 0;
            if (count($this->_commentParser->getThrows()) !== 0) {
                $isSpecialMethod = false;
            }

            foreach ($params as $param) {
                $paramComment = trim($param->getComment());
                $errorPos     = ($param->getLine() + $commentStart);

                if (($isSpecialMethod === true) and ($param->getWhitespaceBeforeType() !== ' ')) {
					$this->_currentFile->addError("Expected 1 space before variable type" , $errorPos, 'OneSpaceVariableFunctionComment');
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeVarName(), ' ');
                if ($spaceCount < $spaceBeforeVar) {
                    $spaceBeforeVar = $spaceCount;
                    $longestType    = $errorPos;
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeComment(), ' ');

                if ($spaceCount < $spaceBeforeComment and $paramComment !== '') {
                    $spaceBeforeComment = $spaceCount;
                    $longestVar         = $errorPos;
                }

                // Make sure they are in the correct order, and have the correct name
                $pos       = $param->getPosition();
                $paramName = ($param->getVarName() !== '') ? $param->getVarName() : '[ UNKNOWN ]';

                if ($previousParam !== null) {
                    $previousName = ($previousParam->getVarName() !== '') ? $previousParam->getVarName() : 'UNKNOWN';

                    // Check to see if the parameters align properly
                    if ($param->alignsVariableWith($previousParam) === false) {
						$this->_currentFile->addError("The variable names for parameters $previousName (".($pos -1).") and $paramName ($pos) do not align" , $errorPos, 'VariablesNamesNotAlignFunctionComment');
                    }

                    if ($param->alignsCommentWith($previousParam) === false) {
						$this->_currentFile->addError("The comments for parameters $previousName (".($pos -1).") and $paramName ($pos) do not align" , $errorPos, 'CommentsNotAlignFunctionComment');
                    }
                }

                // Variable must be one of the supported standard type
                $typeNames = explode('|', $param->getType());
                foreach ($typeNames as $typeName) {
                    $suggestedName = PHP_CodeSniffer::suggestType($typeName);
                    if ($typeName !== $suggestedName) {
						$this->_currentFile->addError("Expected $suggestedName found $typeName for $paramName at position $pos" , $errorPos, 'ExpectedFoundFunctionComment');
                        continue;
                    }

                    if (count($typeNames) !== 1) {
                        continue;
                    }

                    // Check type hint for array and custom type
                    $suggestedTypeHint = '';
                    if (strpos($suggestedName, 'array') !== false) {
                        $suggestedTypeHint = 'array';
                    } else if (in_array($typeName, PHP_CodeSniffer::$allowedTypes) === false) {
                        $suggestedTypeHint = $suggestedName;
                    }

                    if ($suggestedTypeHint !== '' and isset($realParams[($pos - 1)]) === true) {
                        $typeHint = $realParams[($pos - 1)]['type_hint'];
                        if ($typeHint === '') {
							$this->_currentFile->addError("Type hint $suggestedTypeHint missing for $paramName at position $pos" , $commentEnd + 2, 'TypehintMissingFunctionComment');
                        } else if ($typeHint !== $suggestedTypeHint) {
							$this->_currentFile->addError("Expected type hint $suggestedTypeHint found $typeHint for $paramName at position $pos" , $commentEnd + 2, 'ExpectedTypehintFoundFunctionComment');
                        }
                    } else if ($suggestedTypeHint === '' and isset($realParams[($pos - 1)]) === true) {
                        $typeHint = $realParams[($pos - 1)]['type_hint'];
                        if ($typeHint !== '') {
							$this->_currentFile->addError("Unknown type hint $typeHint found for $paramName at position $pos" , $commentEnd + 2, 'UnknowTypehintFoundFunctionComment');
                        }
                    }
                }

                // Make sure the names of the parameter comment matches the
                // actual parameter
                if (isset($realParams[($pos - 1)]) === true) {
                    $realName      = $realParams[($pos - 1)]['name'];
                    $foundParams[] = $realName;

                    // Append ampersand to name if passing by reference
                    if ($realParams[($pos - 1)]['pass_by_reference'] === true) {
                        $realName = '&' . $realName;
                    }

                    if ($realName !== $param->getVarName()) {
						$this->_currentFile->addError("Doc comment var $paramName does not match actual variable name $realName at position $pos" , $errorPos, 'DoccommentNotMatchFunctionComment');
                    }
                } else {
                    // We must have an extra parameter comment
					$this->_currentFile->addError("Superfluous doc comment at position $pos" , $errorPos, 'SuperfluousDoccommentFunctionComment');
                }

                if ($param->getVarName() === '') {
					$this->_currentFile->addError("Missing parameter name at position $pos" , $errorPos, 'MissingParameterFunctionComment');
                }

                if ($param->getType() === '') {
					$this->_currentFile->addError("Missing type at position $pos" , $errorPos, 'MissingTypeFunctionComment');
                }

                if ($paramComment === '') {
                	$this->_currentFile->addError("Missing comment for param $paramName at position $pos" , $errorPos, 'MissingCommentParamFunctionComment');
                } else {

                    // Check if optional params include (Optional) within their description
                    $functionBegin = $this->_currentFile->findNext(array(T_FUNCTION), $commentStart);
                    $functionName  = $this->_currentFile->findNext(array(T_STRING), $functionBegin);
                    $openBracket   = $this->_tokens[$functionBegin]['parenthesis_opener'];
                    $closeBracket  = $this->_tokens[$functionBegin]['parenthesis_closer'];
                    $nextParam     = $this->_currentFile->findNext(T_VARIABLE, ($openBracket + 1), $closeBracket);
                    while ($nextParam !== false) {
                        $nextToken = $this->_currentFile->findNext(T_WHITESPACE, ($nextParam + 1), ($closeBracket + 1), true);
                        if (($nextToken === false) and ($this->_tokens[($nextParam + 1)]['code'] === T_CLOSE_PARENTHESIS)) {
                            break;
                        }

                        $nextCode = $this->_tokens[$nextToken]['code'];
                        $arg      = $this->_tokens[$nextParam]['content'];
                        if (($nextCode === T_EQUAL) and ($paramName === $arg)) {
                            if (substr($paramComment, 0, 11) !== '(Optional) ') {
								$this->_currentFile->addError("Le commentaire pour le parametre $paramName doit commencer avec \"(Optional)\"" , $errorPos, 'OptionalParamStartFunctionComment');
                            }
                        }

                        $nextParam = $this->_currentFile->findNext(T_VARIABLE, ($nextParam + 1), $closeBracket);
                    }
                }

                $previousParam = $param;

            }

            if ($spaceBeforeVar !== 1 and $spaceBeforeVar !== 10000 and $spaceBeforeComment !== 10000) {
				$this->_currentFile->addError("1 espace attendu après la définition du type pour les paramètres" , $longestType, 'OneSpaceLongestTypeFunctionComment');
            }

            if ($spaceBeforeComment !== 1 and $spaceBeforeComment !== 10000) {
				$this->_currentFile->addError("1 espace attendu après le nom de la variable pour les paramètres" , $longestVar, 'OneSpaceLongestTypeFunctionComment');
            }
        }

        $realNames = array();
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report missing comments
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            if (count($params) !== 0) {
                $errorPos = ($params[(count($params) - 1)]->getLine() + $commentStart);
            } else {
                $errorPos = $commentStart;
            }

            $this->_currentFile->addError("Commentaire de fonction manquant ($neededParam)" , $errorPos, 'DoccommentMissingFunctionComment');
        }
    }
}
