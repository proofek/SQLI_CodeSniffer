<?php

class GN_Tests_Classes_ClassFileNameUnitTest extends AbstractSQLISniffUnitTest
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
	        //case 'ClassFileNameUnitTest2.php':
	        //    $events1[] = new SQLI_CodeSniffer_Event(2, 1, 'MATCH_CLASS_NAME');
	        //    
	        //    return new SQLI_CodeSniffer_EventList($events1);
	        default:
	            return array();
	            break;
        }
    }
}