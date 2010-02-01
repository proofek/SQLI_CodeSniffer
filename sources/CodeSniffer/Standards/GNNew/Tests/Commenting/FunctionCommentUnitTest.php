<?php

class GNNew_Tests_Commenting_FunctionCommentUnitTest extends AbstractSniffUnitTest
{
	/**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        	case 'FunctionCommentUnitTest.1.inc':
	            return array(7 => 1);
        	case 'FunctionCommentUnitTest.2.inc':
        		return array(2 => 1);
        	case 'FunctionCommentUnitTest.3.inc':
        		return array(3 => 1);
        	case 'FunctionCommentUnitTest.5.inc':
	            return array(2 => 1);
        	case 'FunctionCommentUnitTest.7.inc':
        		return array(6 => 1);
        	case 'FunctionCommentUnitTest.8.inc':
				return array(7 => 1);
	        case 'FunctionCommentUnitTest.9.inc':
				return array(5 => 1);
	        case 'FunctionCommentUnitTest.10.inc':
	        	return array(6 => 1);
	        case 'FunctionCommentUnitTest.11.inc':
	        	return array(4 => 1);
	        case 'FunctionCommentUnitTest.12.inc':
	        	return array(9 => 1);
	        case 'FunctionCommentUnitTest.13.inc':
	        	return array(10 => 1);
	        default:
	            return array();
	            break;
	        //TAG_NOTALLOWED_FUNCTION_COMMENT ???? comment teste
	        //PARAM_TAG_ORDER_FUNCTION_COMMENT
	        //SUPERFLUOUS_DOCCOMMENT_FUNCTION_COMMENT ????? variable ds le commentaire mais pas ds parmatres de la fonction
	    }
    }//end getErrorList()

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList($testFile='')
    {
        return array();
    }//end getWarningList()
}