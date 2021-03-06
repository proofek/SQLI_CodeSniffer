<?php

class GN_Tests_WhiteSpace_ScopeClosingBraceUnitTest extends AbstractSQLISniffUnitTest
{

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return SQLI_CodeSniffer_EventList
     */
    public function getEventList($testFile='')
    {
        switch ($testFile) {
	        case 'ScopeClosingBraceUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(2, 13, 'ONELINE_SCOPE_CLOSINGBRACE');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        default:
	            return array();
	            break;
        }
           	
    }
}