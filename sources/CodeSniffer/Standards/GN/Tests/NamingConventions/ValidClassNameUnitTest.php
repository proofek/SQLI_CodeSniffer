<?php

class GN_Tests_NamingConventions_ValidClassNameUnitTest extends AbstractSQLISniffUnitTest
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
	        case 'ValidClassNameUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_START_LETTER_VALID_CLASS');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        case 'ValidClassNameUnitTest.2.inc':
	            $events2[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_NOT_VALID_NEWNAME_VALID_CLASS');	            
	            return new SQLI_CodeSniffer_EventList($events2);
	        default:
	            return array();
	            break;
        }
           	
    }
}