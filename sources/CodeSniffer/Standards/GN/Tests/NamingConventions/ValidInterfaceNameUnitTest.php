<?php

class GN_Tests_NamingConventions_ValidInterfaceNameUnitTest extends AbstractSQLISniffUnitTest
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
	        case 'ValidInterfaceNameUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_END_VALID_INTERFACE');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        default:
	            return array();
	            break;
        }
           	
    }
}