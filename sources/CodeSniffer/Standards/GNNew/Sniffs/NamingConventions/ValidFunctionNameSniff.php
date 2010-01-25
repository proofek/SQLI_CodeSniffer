<?php
/**
 * GNNew_Sniffs_NamingConventions_ValidFunctionNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * GNNew_Sniffs_NamingConventions_ValidFunctionNameSniff.
 * Stripped version of PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff
 *
 * Ensures method names are correct depending on whether they are public
 * or private, and that functions are named correctly.
 *
 * @category  PHP
 * @package   SQLI_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GNNew_Sniffs_NamingConventions_ValidFunctionNameSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    private $_magicMethods = array(
                              'construct',
                              'destruct',
                              'call',
                              'callStatic',
                              'get',
                              'set',
                              'isset',
                              'unset',
                              'sleep',
                              'wakeup',
                              'toString',
                              'set_state',
                              'clone',
                             );

    /**
     * A list of all PHP magic functions.
     *
     * @var array
     */
    private $_magicFunctions = array(
                                'autoload',
                               );


    /**
     * Constructs a PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);

    }//end __construct()


    /**
     * Processes the tokens within the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                  $stackPtr  The position where this token was
     *                                        found.
     * @param int                  $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $className  = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = substr($methodName, 2);
            if (in_array($magicPart, $this->_magicMethods) === false) {
			    $phpcsFile->addError('La fonction de nom "'.$className.'::'.$methodName.'" est invalide, seules les méthodes PHP magic methods doivent être préfixés par un double souligné' , $stackPtr, 'NameInvalidMagicMethodValidFunction'); 
            }
            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodName === '_'.$className) {
            return;
        }

        $methodProps    = $phpcsFile->getMethodProperties($stackPtr);
        $isPublic       = ($methodProps['scope'] === 'private') ? false : true;
        $scope          = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

//        // If it's a private method, it must have an underscore on the front.
//        if ($isPublic === false && $methodName{0} !== '_') {
//            $error = "Private method name \"$className::$methodName\" must be prefixed with an underscore";
//            $phpcsFile->addError($error, $stackPtr);
//            return;
//        }
//
//        // If it's not a private method, it must not have an underscore on the front.
//        if ($isPublic === true && $scopeSpecified === true && $methodName{0} === '_') {
//            $error = ucfirst($scope)." method name \"$className::$methodName\" must not be prefixed with an underscore";
//            $phpcsFile->addError($error, $stackPtr);
//            return;
//        }

        // If the scope was specified on the method, then the method must be
        // camel caps and an underscore should be checked for. If it wasn't
        // specified, treat it like a public method and remove the underscore
        // prefix if there is one because we cant determine if it is private or
        // public.
        $testMethodName = $methodName;
        if ($scopeSpecified === false && $methodName{0} === '_') {
            $testMethodName = substr($methodName, 1);
        }

        if (PHP_CodeSniffer::isCamelCaps($testMethodName, false, $isPublic, false) === false) {
            if ($scopeSpecified === true) {
			    $phpcsFile->addError('La méthode '.ucfirst($scope).' "'.$className.'::'.$methodName.'" n\'est pas au format camelCase' , $stackPtr, 'ScopeMethodNameNotCamelValidFunction'); 
            } else {
            	$phpcsFile->addError('La méthode "'.$className.'::'.$methodName.'" n\'est pas au format camelCase' , $stackPtr, 'MethodNameNotCamelValidFunction'); 
			}
            return;
        }

    }//end processTokenWithinScope()


    /**
     * Processes the tokens outside the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                  $stackPtr  The position where this token was
     *                                        found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        // Is this a magic function. IE. is prefixed with "__".
        if (preg_match('|^__|', $functionName) !== 0) {
            $magicPart = substr($functionName, 2);
            if (in_array($magicPart, $this->_magicFunctions) === false) {
			     $phpcsFile->addError("La fonction de nom $functionName est invalide; seulement les méthodes PHP magic methods doivent être préfixés par un double souligné" , $stackPtr, 'NameInvalidMagicMethodValidFunction'); 
            }

            return;
        }
        
        // Function names can be in two parts; the package name and
        // the function name.
        //$packagePart   = '';
        $camelCapsPart = '';
        $underscorePos = strrpos($functionName, '_');
        if ($underscorePos === false) {
            $camelCapsPart = $functionName;
        } else {
            //$packagePart   = substr($functionName, 0, $underscorePos);
            $camelCapsPart = substr($functionName, ($underscorePos + 1));

            // We don't care about _'s on the front.
            //$packagePart = ltrim($packagePart, '_');
        }

        // If it has a package part, make sure the first letter is a capital.
        //if ($packagePart !== '') {
            if ($functionName{0} === '_') {
			    $phpcsFile->addError("La fonction de nom $functionName est invalide; seul les méthodes privées doivent être préfixées par un soulignement" , $stackPtr, 'NameInvalidPrivateMethodValidFunction'); 
                return;
            }

            /*
            if ($functionName{0} !== strtoupper($functionName{0})) {
			    $phpcsFile->addEvent(
			        'NAME_PREFIXED_START_CAPITAL_LETTER_VALID_FUNCTION', 
			        array('name' => $functionName),
			        $stackPtr
			    );        	
                return;
            }
			*/
        //}

        // If it doesn't have a camel caps part, it's not valid.
        if (trim($camelCapsPart) === '') {
			$phpcsFile->addError("Le nom de la fonction $functionName n'est pas valide" , $stackPtr, 'NameInvalidIncompleteValidFunction'); 
            return;
        }

        $validName        = true;
        $newPackagePart   = $packagePart;
        $newCamelCapsPart = $camelCapsPart;

        // Every function must have a camel caps part, so check that first.
        if (PHP_CodeSniffer::isCamelCaps($camelCapsPart, false, true, false) === false) {
            $validName        = false;
            $newCamelCapsPart = strtolower($camelCapsPart{0}).substr($camelCapsPart, 1);
        }

        if ($packagePart !== '') {
            // Check that each new word starts with a capital.
            $nameBits = explode('_', $packagePart);
            foreach ($nameBits as $bit) {
                if ($bit{0} !== strtoupper($bit{0})) {
                    $newPackagePart = '';
                    foreach ($nameBits as $bit) {
                        $newPackagePart .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = false;
                    break;
                }
            }
        }

        if ($validName === false) {
            $newName = rtrim($newPackagePart, '_').'_'.$newCamelCapsPart;
            if ($newPackagePart === '') {
                $newName = $newCamelCapsPart;
            } else {
                $newName = rtrim($newPackagePart, '_').'_'.$newCamelCapsPart;
            }
            
			$phpcsFile->addError("Nom de fonction $functionName invalide; $newName attendu" , $stackPtr, 'NameInvalidConsiderNewnameValidFunction'); 
        }

    }//end processTokenOutsideScope()


}//end class

?>
