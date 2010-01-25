<?php

class GNNew_Tests_WhiteSpace_IncrementDecrementSpacingUnitTest extends AbstractSQLISniffUnitTest
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
	        case 'IncrementDecrementSpacingUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(2, 4, 'EXPECTED_SPACE_DECREMENTSPACING');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        default:
	            return array();
	            break;
        }
           	
    }
}