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
 * GN_Sniffs_Commenting_FunctionCommentSniff
 * Stripped version of Zend_Sniffs_Commenting_FunctionCommentSniff
 *
 * Parses and verifies the doc comments for functions
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GN_Sniffs_Commenting_FunctionCommentSniff implements SQLI_CodeSniffer_Sniff
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
		    $phpcsFile->addEvent(
		       'STYLE_FUNCTION_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
            return;
        } else if ($code !== T_DOC_COMMENT) {
		    $phpcsFile->addEvent(
		       'MISSING_FUNCTION_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
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
		    $phpcsFile->addEvent(
		       'STYLE_FUNCTION_COMMENT', 
		       array(),
		       $stackPtr
		    );        	
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
		    $phpcsFile->addEvent(
		       'ERROR_PARSING_FUNCTION_COMMENT', 
		       array(),
		       $line
		    );        	
            return;
        }

        $comment = $this->_commentParser->getComment();
        if (is_null($comment) === true) {
		    $phpcsFile->addEvent(
		       'FUNCTION_COMMENT_EMPTY', 
		       array(),
		       $commentStart
		    );        	
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
					    $this->_currentFile->addEvent(
					       'PARAM_TAG_ORDER_FUNCTION_COMMENT', 
					       array(),
					       ($commentStart + $tag)
					    );        	
                    }
                    break;

                case 'return':
                    if (($con !== 'comment') and ($con !== 'param')) {
					    $this->_currentFile->addEvent(
					       'PARAM_TAG_ORDER_FUNCTION_COMMENT', 
					       array(),
					       ($commentStart + $tag)
					    );        	
                    }
                    break;

                default:
					    $this->_currentFile->addEvent(
					       'TAG_NOTALLOWED_FUNCTION_COMMENT', 
					       array('tagname' => $content),
					       ($commentStart + $tag)
					    );        	
                    break;
            }

            $con = $content;
        }

        $this->_processParams($commentStart, $commentEnd);
        $this->_processReturn($commentStart, $commentEnd);

        // Check for a comment description
        $short = $comment->getShortComment();
        if (trim($short) === '') {
		    $phpcsFile->addEvent(
		       'MISSING_SHORT_DESC_FUNCTION_COMMENT', 
		       array(),
		       $commentStart
		    );        	
            return;
        }

        // No extra newline before short description
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' and $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
		    $phpcsFile->addEvent(
		       'EXTRA_LINE_FUNCTION_COMMENT', 
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
                $error = '';
			    $phpcsFile->addEvent(
			       'BLANK_LINE_FUNCTION_COMMENT', 
			       array(),
			       ($commentStart + $newlineCount + 1)
			    );        	
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

			    $phpcsFile->addEvent(
			       'BLANK_LINE_TAGS_FUNCTION_COMMENT', 
			       array(),
			       ($commentStart + $newlineCount)
			    );        	
                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Check for unknown/deprecated tags
        $unknownTags = $this->_commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $tagname = $errorTag['tag'];
			$phpcsFile->addEvent(
			   'TAG_NOTALLOWED_FUNCTION_COMMENT', 
			   array('tagname' => $tagname),
			       $commentStart + $errorTag['line']
			);        	
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
				$this->_currentFile->addEvent(
				   'TAG_RETURN_NOT_REQUIRED_FUNCTION_COMMENT', 
				   array(),
				       $errorPos
				);        	                
            }

            return;
        }

        if ($return === null) {
            $error = 'Missing @return tag in function comment';
//            $this->_currentFile->addError($error, $commentEnd);
			$this->_currentFile->addEvent(
			   'TAG_RETURN_MISSING_FUNCTION_COMMENT', 
			   array(),
			       $commentEnd
			);        	                
            return;
        }

        $tagOrder = $this->_commentParser->getTagOrders();
        $index    = array_keys($tagOrder, 'return');
        $errorPos = ($commentStart + $return->getLine());
        $content  = trim($return->getRawContent());

        if (count($index) > 1) {
			$this->_currentFile->addEvent(
			   'ONE_VERSION_TAG_FUNCTION_COMMENT', 
			   array(),
			       $errorPos
			);        	                
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
			$this->_currentFile->addEvent(
			   'TYPE_MISSING_TAG_RETURN_FUNCTION_COMMENT', 
			   array(),
			       $errorPos
			);        	                
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
				$this->_currentFile->addEvent(
				   'TYPE_RETURN_INVALID_FUNCTION_COMMENT', 
				   array('content' => $content),
				       $errorPos
				);        	                
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
							$this->_currentFile->addEvent(
							   'TYPE_RETURN_VOID_FUNCTION_COMMENT', 
							   array(),
							       $errorPos
							);        	                
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
						$this->_currentFile->addEvent(
						   'TYPE_RETURN_NOT_VOID_NO_STATEMENT_FUNCTION_COMMENT', 
						   array(),
						       $errorPos
						);        	                
                    } else {
                        $semicolon = $this->_currentFile->findNext(T_WHITESPACE, ($return + 1), null, true);
                        if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
							$this->_currentFile->addEvent(
							   'TYPE_RETURN_NOT_VOID_FUNCTION_COMMENT', 
							   array(),
							       $errorPos
							);        	                
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
				$this->_currentFile->addEvent(
				   'EMPTY_LINE_LAST_PARAMETER_FUNCTION_COMMENT', 
				   array(),
				   $errorPos + 1
				);        	                
            }

            // Parameters must appear immediately after the comment
            if ($params[0]->getOrder() !== 2) {
                $errorPos = ($params[0]->getLine() + $commentStart);
				$this->_currentFile->addEvent(
				   'PARAMETER_AFTER_COMMENT_FUNCTION_COMMENT', 
				   array(),
				   $errorPos
				);        	                
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
					$this->_currentFile->addEvent(
					   'ONE_SPACE_VARIABLE_FUNCTION_COMMENT', 
					   array(),
					   $errorPos
					);        	                
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
						$this->_currentFile->addEvent(
						   'VARIABLES_NAMES_NOT_ALIGN_FUNCTION_COMMENT', 
						   array('previousname' => $previousName, 'previousnamepos' => $pos -1,
						   		 'paramname' => $paramName, 'paramnamepos' => $pos),
						   $errorPos
						);        	                
                    }

                    if ($param->alignsCommentWith($previousParam) === false) {
						$this->_currentFile->addEvent(
						   'COMMENTS_NOT_ALIGN_FUNCTION_COMMENT', 
						   array('previousname' => $previousName, 'previousnamepos' => $pos -1,
						   		 'paramname' => $paramName, 'paramnamepos' => $pos),
						   $errorPos
						);        	                
                    }
                }

                // Variable must be one of the supported standard type
                $typeNames = explode('|', $param->getType());
                foreach ($typeNames as $typeName) {
                    $suggestedName = PHP_CodeSniffer::suggestType($typeName);
                    if ($typeName !== $suggestedName) {
						$this->_currentFile->addEvent(
						   'EXPECTED_FOUND_FUNCTION_COMMENT', 
						   array('suggestedname' => $suggestedName, 'paramname' => $paramName, 
						   		 'typename' => $paramName, 'paramnamepos' => $pos),
						   $errorPos
						);        	                
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
							$this->_currentFile->addEvent(
							   'TYPEHINT_MISSING_FUNCTION_COMMENT', 
							   array('suggestedtypehint' => $suggestedTypeHint, 'paramname' => $paramName, 
							   		 'paramnamepos' => $pos),
							   $commentEnd + 2
							);        	                
                        } else if ($typeHint !== $suggestedTypeHint) {
							$this->_currentFile->addEvent(
							   'EXPECTED_TYPEHINT_FOUND_FUNCTION_COMMENT', 
							   array('suggestedtypehint' => $suggestedTypeHint, 'typehint' => $typeHint, 
							   		 'paramname' => $paramName, 'paramnamepos' => $pos),
							   $commentEnd + 2
							);        	                
                        }
                    } else if ($suggestedTypeHint === '' and isset($realParams[($pos - 1)]) === true) {
                        $typeHint = $realParams[($pos - 1)]['type_hint'];
                        if ($typeHint !== '') {
							$this->_currentFile->addEvent(
							   'UNKNOW_TYPEHINT_FOUND_FUNCTION_COMMENT', 
							   array('typehint' => $typeHint, 
							   		 'paramname' => $paramName, 'paramnamepos' => $pos),
							   $commentEnd + 2
							);        	                
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
						$this->_currentFile->addEvent(
						   'DOCCOMMENT_NOT_MATCH_FUNCTION_COMMENT', 
						   array('paramname' => $paramName, 'realname' => $realName, 
						         'paramnamepos' => $pos),
						   $errorPos
						);        	                
                    }
                } else {
                    // We must have an extra parameter comment
					$this->_currentFile->addEvent(
					   'SUPERFLUOUS_DOCCOMMENT_FUNCTION_COMMENT', 
					   array('doccommentpos' => $pos),
					   $errorPos
					);        	                
                }

                if ($param->getVarName() === '') {
					$this->_currentFile->addEvent(
					   'MISSING_PARAMETER_FUNCTION_COMMENT', 
					   array('parameterpos' => $pos),
					   $errorPos
					);        	                
                }

                if ($param->getType() === '') {
					$this->_currentFile->addEvent(
					   'MISSING_TYPE_FUNCTION_COMMENT', 
					   array('typepos' => $pos),
					   $errorPos
					);        	                
                }

                if ($paramComment === '') {
					$this->_currentFile->addEvent(
					   'MISSING_COMMENT_PARAM_FUNCTION_COMMENT', 
					   array('paramname' => $paramName, 'paramnamepos' => $pos),
					   $errorPos
					);        	                
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
								$this->_currentFile->addEvent(
								   'OPTIONAL_PARAM_START_FUNCTION_COMMENT', 
								   array('paramname' => $paramName),
								   $errorPos
								);        	                
                            }
                        }

                        $nextParam = $this->_currentFile->findNext(T_VARIABLE, ($nextParam + 1), $closeBracket);
                    }
                }

                $previousParam = $param;

            }

            if ($spaceBeforeVar !== 1 and $spaceBeforeVar !== 10000 and $spaceBeforeComment !== 10000) {
				$this->_currentFile->addEvent(
				   'ONE_SPACE_LONGEST_TYPE_FUNCTION_COMMENT', 
				   array(),
				   $longestType
				);        	                
            }

            if ($spaceBeforeComment !== 1 and $spaceBeforeComment !== 10000) {
				$this->_currentFile->addEvent(
				   'ONE_SPACE_LONGEST_VARIABLE_FUNCTION_COMMENT', 
				   array(),
				   $longestVar
				);        	                
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

			$this->_currentFile->addEvent(
			   'DOCCOMMENT_MISSING_FUNCTION_COMMENT', 
			   array('param' => $neededParam),
			   $errorPos
			);        	                
        }
    }
}
