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
class PHP_CodeSniffer_Standards_Symfony_SymfonyCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The Symfony standard uses some others sniffs.
     *
     * @return array
     */
    public function getIncludedSniffs ()
    {
        return array(
            'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',
        );
    }
}