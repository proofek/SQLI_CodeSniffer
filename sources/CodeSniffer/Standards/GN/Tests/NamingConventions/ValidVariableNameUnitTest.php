<?php

class GN_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSQLISniffUnitTest
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
	        case 'ValidVariableNameUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(2, 1, 'NOT_VALID_CAMEL_VALID_VARIABLE');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        case 'ValidVariableNameUnitTest.2.inc':
	            $events2[] = new SQLI_CodeSniffer_Event(3, 9, 'ATTR_NOT_VALID_CAMEL_VALID_VARIABLE');	            
	            return new SQLI_CodeSniffer_EventList($events2);
	        case 'ValidVariableNameUnitTest.3.inc':
	            $events3[] = new SQLI_CodeSniffer_Event(3, 9, 'NO_START_UNDERSCORE_VALID_VARIABLE');	            
	            return new SQLI_CodeSniffer_EventList($events3);
	        case 'ValidVariableNameUnitTest.4.inc':
	            $events4[] = new SQLI_CodeSniffer_Event(2, 1, 'CONTAINS_NUMBER_VALID_VARIABLE');	            
	            return new SQLI_CodeSniffer_EventList($events4);
	        default:
	            return array();
	            break;
        }
           	
    }
}