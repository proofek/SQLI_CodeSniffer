<?php

class GNNew_Tests_Commenting_VariableCommentUnitTest extends AbstractSQLISniffUnitTest
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
        	case 'VariableCommentUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(8, 10, 'MISSING_VARIABLE_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events1);
        	case 'VariableCommentUnitTest.2.inc':
	            $events2[] = new SQLI_CodeSniffer_Event(11, 10, 'STYLE_VARIABLE_COMMENT');	  
	            return new SQLI_CodeSniffer_EventList($events2);
        	case 'VariableCommentUnitTest.3.inc':
	            $events3[] = new SQLI_CodeSniffer_Event(8, 5, 'VARIABLE_COMMENT_EMPTY');	            
	            return new SQLI_CodeSniffer_EventList($events3);	  	                      	            
        	case 'VariableCommentUnitTest.4.inc':
	            $events4[] = new SQLI_CodeSniffer_Event(8, 5, 'MISSING_SHORT_DESC_VARIABLE_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events4);	  	                      	            
        	case 'VariableCommentUnitTest.5.inc':
	            $events5[] = new SQLI_CodeSniffer_Event(9, 1, 'EXTRA_LINE_VARIABLE_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events5);
        	case 'VariableCommentUnitTest.6.inc':
	            $events6[] = new SQLI_CodeSniffer_Event(9, 1, 'BLANK_LINE_BEFORE_VARIABLE_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events6);	                                  	           
        	case 'VariableCommentUnitTest.7.inc':
	            $events7[] = new SQLI_CodeSniffer_Event(10, 1, 'BLANK_LINE_BETWEEN_VARIABLE_COMMENT');	            
	         	return new SQLI_CodeSniffer_EventList($events7);	                                  	           
        	case 'VariableCommentUnitTest.8.inc':
	            $events8[] = new SQLI_CodeSniffer_Event(9, 1, 'SHORT_DESC_VARIABLE_COMMENT');	            
	         	return new SQLI_CodeSniffer_EventList($events8);	                                  	           
	        default:
	            return array();
	            break;
        }
    }
}