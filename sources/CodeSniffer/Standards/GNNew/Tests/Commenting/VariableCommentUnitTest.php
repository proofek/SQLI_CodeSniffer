<?php

class GNNew_Tests_Commenting_VariableCommentUnitTest extends AbstractSniffUnitTest
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
        	case 'VariableCommentUnitTest.1.inc':
        		return array(8 => 1);
        	case 'VariableCommentUnitTest.2.inc':
	            return array(11 => 1);
        	case 'VariableCommentUnitTest.3.inc':
        		return array(8 => 1);
        	case 'VariableCommentUnitTest.4.inc':
        		return array(8 => 1);
        	case 'VariableCommentUnitTest.5.inc':
        		return array(9 => 1);
        	case 'VariableCommentUnitTest.6.inc':
        		return array(9 => 1);
        	case 'VariableCommentUnitTest.7.inc':
        		return array(10 => 1);
        	case 'VariableCommentUnitTest.8.inc':
        		return array(9 => 1);
	        default:
	            return array();
	            break;
        }
    }
    
    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
    	return array();
    }//end getWarningList()
}