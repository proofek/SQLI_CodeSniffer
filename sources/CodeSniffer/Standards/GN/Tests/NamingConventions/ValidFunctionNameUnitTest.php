<?php

class GN_Tests_NamingConventions_ValidFunctionNameUnitTest extends AbstractSQLISniffUnitTest
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
	        case 'ValidFunctionNameUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(3, 1, 'METHOD_NAME_NOT_CAMEL_VALID_FUNCTION');	            
	            return new SQLI_CodeSniffer_EventList($events1);
	        case 'ValidFunctionNameUnitTest.2.inc':
	            $events2[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_INVALID_MAGIC_METHOD_VALID_FUNCTION');	            
	            return new SQLI_CodeSniffer_EventList($events2);
	        case 'ValidFunctionNameUnitTest.3.inc':
	            $events3[] = new SQLI_CodeSniffer_Event(3, 8, 'SCOPE_METHOD_NAME_NOT_CAMEL_VALID_FUNCTION');	            
	            return new SQLI_CodeSniffer_EventList($events3);	            
	        case 'ValidFunctionNameUnitTest.4.inc':
	            $events4[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_INVALID_CONSIDER_NEWNAME_VALID_FUNCTION');	            
	            return new SQLI_CodeSniffer_EventList($events4);	            
	        case 'ValidFunctionNameUnitTest.5.inc':
	            $events5[] = new SQLI_CodeSniffer_Event(2, 1, 'NAME_INVALID_PRIVATE_METHOD_VALID_FUNCTION');	            
	            return new SQLI_CodeSniffer_EventList($events5);	            
	        default:
	            return array();
	            break;
        }
           	
    }
}