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
if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Zend_Sniffs_NamingConventions_ValidFunctionNameSniff
 *
 * Ensures method names are correct depending on whether they are public
 * or private, and that functions are named correctly
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_NamingConventions_ValidFunctionNameSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * A list of all PHP magic methods
     *
     * @var array
     */
    private $_magicMethods = array('construct', 'destruct', 'call',   'callStatic', 'get',       'set', 'isset',
                                   'unset',     'sleep',    'wakeup', 'toString',   'set_state', 'clone');

    /**
     * A list of all PHP magic functions
     *
     * @var array
     */
    private $_magicFunctions = array('autoload');

    /**
     * Constructs a PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);

    }

    /**
     * Processes the tokens within the scope
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being processed
     * @param  integer              $stackPtr  The position where this token was found
     * @param  integer              $currScope The position of the current scope
     * @return void
     */
    public function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $className  = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        // Is this a magic method. IE is prefixed with "__"
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = substr($methodName, 2);
            if (in_array($magicPart, $this->_magicMethods) === false) {
                 $error = "Method name \"$className::$methodName\" is invalid; "
                        . 'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsFile->addError($error, $stackPtr);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules
        if ($methodName === '_' . $className) {
            return;
        }

        $methodProps    = $phpcsFile->getMethodProperties($stackPtr);
        $isPublic       = ($methodProps['scope'] === 'public') ? true : false;
        $scope          = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

        // If it's not a public method, it must have an underscore on the front
        if ($isPublic === false and $methodName{0} !== '_') {
            $error = ucfirst($scope) . " method name \"$className::$methodName\" must be prefixed with an underscore";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // If it's a public method, it must not have an underscore on the front
        if ($isPublic === true and $scopeSpecified === true and $methodName{0} === '_') {
            $error = ucfirst($scope) . " method name \"$className::$methodName\" must not be prefixed "
                   . 'with an underscore';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // If the scope was specified on the method, then the method must be camel caps and an underscore should
        // be checked for. If it wasn't specified, treat it like a public method and remove the underscore
        // prefix if there is one because we cant determine if it is private or public
        $testMethodName = $methodName;
        if ($scopeSpecified === false and $methodName{0} === '_') {
            $testMethodName = substr($methodName, 1);
        }

        if (PHP_CodeSniffer::isCamelCaps($testMethodName, false, $isPublic, false) === false) {
            if ($scopeSpecified === true) {
                $error = ucfirst($scope) . " method name \"$className::$methodName\" is not in camel caps format";
            } else {
                $error = "Method name \"$className::$methodName\" is not in camel caps format";
            }

            $phpcsFile->addError($error, $stackPtr);
            return;
        }
    }

    /**
     * Processes the tokens outside the scope
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being processed
     * @param  integer              $stackPtr  The position where this token was found
     * @return void
     */
    public function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        // Is this a magic function. IE. is prefixed with "__"
        if (preg_match('|^__|', $functionName) !== 0) {
            $magicPart = substr($functionName, 2);
            if (in_array($magicPart, $this->_magicFunctions) === false) {
                 $error = 'Function name ' . $functionName . ' is invalid; only PHP magic methods should be '
                        . 'prefixed with a double underscore';
                 $phpcsFile->addError($error, $stackPtr);
            }

            return;
        }

        // Function names can be in two parts; the package name and
        // the function name
        $packagePart   = '';
        $camelCapsPart = '';
        $underscorePos = strrpos($functionName, '_');
        if ($underscorePos === false) {
            $camelCapsPart = $functionName;
        } else {
            $packagePart   = substr($functionName, 0, $underscorePos);
            $camelCapsPart = substr($functionName, ($underscorePos + 1));

            // We don't care about _'s on the front
            $packagePart = ltrim($packagePart, '_');
        }

        // If it has a package part, make sure the first letter is a capital
        if ($packagePart !== '') {
            if ($functionName{0} === '_') {
                $error = 'Function name ' . $functionName . ' is invalid; only private methods should be '
                       . 'prefixed with an underscore';
                $phpcsFile->addError($error, $stackPtr);
                return;
            }

            if ($functionName{0} !== strtoupper($functionName{0})) {
                $error = 'Function name ' . $functionName . ' is prefixed with a package name but does '
                       . 'not begin with a capital letter';
                $phpcsFile->addError($error, $stackPtr);
                return;
            }
        }

        // If it doesn't have a camel caps part, it's not valid
        if (trim($camelCapsPart) === '') {
            $error = "Function name \"$functionName\" is not valid; name appears incomplete";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        $validName        = true;
        $newPackagePart   = $packagePart;
        $newCamelCapsPart = $camelCapsPart;

        // Every function must have a camel caps part, so check that first
        if (PHP_CodeSniffer::isCamelCaps($camelCapsPart, false, true, false) === false) {
            $validName        = false;
            $newCamelCapsPart = strtolower($camelCapsPart{0}) . substr($camelCapsPart, 1);
        }

        if ($packagePart !== '') {
            // Check that each new word starts with a capital
            $nameBits = explode('_', $packagePart);
            foreach ($nameBits as $bit) {
                if ($bit{0} !== strtoupper($bit{0})) {
                    $newPackagePart = '';
                    foreach ($nameBits as $bit) {
                        $newPackagePart .= strtoupper($bit{0}) . substr($bit, 1) . '_';
                    }

                    $validName = false;
                    break;
                }
            }
        }

        if ($validName === false) {
            $newName = rtrim($newPackagePart, '_') . '_' . $newCamelCapsPart;
            if ($newPackagePart === '') {
                $newName = $newCamelCapsPart;
            } else {
                $newName = rtrim($newPackagePart, '_') . '_' . $newCamelCapsPart;
            }

            $error = "Function name \"$functionName\" is invalid; consider \"$newName\" instead";
            $phpcsFile->addError($error, $stackPtr);
        }
    }
}
