<?php

class GN_Tests_Commenting_FunctionCommentUnitTest extends AbstractSQLISniffUnitTest
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
        	case 'FunctionCommentUnitTest.1.inc':
	            $events1[] = new SQLI_CodeSniffer_Event(7, 1, 'STYLE_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events1);
        	case 'FunctionCommentUnitTest.2.inc':
	            $events2[] = new SQLI_CodeSniffer_Event(2, 1, 'FUNCTION_COMMENT_EMPTY');	            
	            return new SQLI_CodeSniffer_EventList($events2);
        	case 'FunctionCommentUnitTest.3.inc':
	            $events3[] = new SQLI_CodeSniffer_Event(3, 1, 'MISSING_SHORT_DESC_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events3);
        	case 'FunctionCommentUnitTest.5.inc':
	            $events5[] = new SQLI_CodeSniffer_Event(2, 1, 'MISSING_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events5);
        	case 'FunctionCommentUnitTest.7.inc':
	            $events7[] = new SQLI_CodeSniffer_Event(6, 1, 'ONE_SPACE_LONGEST_TYPE_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events7);	            
        	case 'FunctionCommentUnitTest.8.inc':
	            $events8[] = new SQLI_CodeSniffer_Event(7, 1, 'ONE_SPACE_LONGEST_VARIABLE_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events8);
	        case 'FunctionCommentUnitTest.9.inc':
	            $events9[] = new SQLI_CodeSniffer_Event(5, 1, 'BLANK_LINE_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events9);
	        case 'FunctionCommentUnitTest.10.inc':
	            $events10[] = new SQLI_CodeSniffer_Event(6, 1, 'BLANK_LINE_TAGS_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events10);	            
	        case 'FunctionCommentUnitTest.11.inc':
	            $events11[] = new SQLI_CodeSniffer_Event(4, 1, 'EXTRA_LINE_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events11);	            
	        case 'FunctionCommentUnitTest.12.inc':
	            $events12[] = new SQLI_CodeSniffer_Event(9, 1, 'OPTIONAL_PARAM_START_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events12);	            
	        case 'FunctionCommentUnitTest.13.inc':
	            $events13[] = new SQLI_CodeSniffer_Event(10, 1, 'TYPE_RETURN_NOT_VOID_NO_STATEMENT_FUNCTION_COMMENT');	            
	            return new SQLI_CodeSniffer_EventList($events13);	            
	        default:
	            return array();
	            break;
	        //TAG_NOTALLOWED_FUNCTION_COMMENT ???? comment teste
	        //PARAM_TAG_ORDER_FUNCTION_COMMENT
	        //SUPERFLUOUS_DOCCOMMENT_FUNCTION_COMMENT ????? variable ds le commentaire mais pas ds parmatres de la fonction
	        
        }
    }
}