<?php
/**
 * Test Coding Standard.
 *
 */
if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Test Coding Standard.
 *
 */
class PHP_CodeSniffer_Standards_GNNew_GNNewCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The Zend standard uses some PEAR sniffs.
     *
     * @return array
     */
// TODO : recover this!
//    public function getIncludedSniffs ()
//    {
//        return array(
//        //CNM
//            'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php', // OK
//            'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php', // OK
//
//        //CPC
//            'Generic/Sniffs/Formatting/DisallowMultipleStatementsSniff.php', // OK
//            'Zend/Sniffs/WhiteSpace/OperatorSpacingSniff.php', // OK
//            'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php', // OK
//            'Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php', // OK
//            'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php', // OK
//            'Squiz/Sniffs/WhiteSpace/SemicolonSpacingSniff.php', // OK
//            'Zend/Sniffs/Functions/FunctionDeclarationArgumentSpacingSniff.php', // OK
//            'Zend/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php', // OK
//            'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php', // OK
//        
//        //CAD
//        
//        //CCE
//            'Squiz/Sniffs/PHP/DisallowMultipleAssignmentsSniff.php', // OK
//        
//        //DCR
//            'Squiz/Sniffs/Formatting/OperatorBracketSniff.php', // OK
//        //VUR
//            'Generic/Sniffs/CodeAnalysis/UnusedFunctionParameterSniff.php',  //OK
//
//        );
//    }
}