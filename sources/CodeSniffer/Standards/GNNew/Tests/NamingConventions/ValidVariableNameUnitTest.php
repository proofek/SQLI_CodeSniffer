<?php

class GNNew_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest
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
	        case 'ValidVariableNameUnitTest.1.inc':
	            return array(2 => 1);
	        case 'ValidVariableNameUnitTest.2.inc':
	            return array(3 => 1);
	        case 'ValidVariableNameUnitTest.3.inc':
	            return array(3 => 1);	        
	        default:
	            return array();
	            break;
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
        switch ($testFile) {
	        case 'ValidVariableNameUnitTest.4.inc':
	        	return array(2 => 1);	              
	        default:
	            return array();
	            break;
        }  	
    }//end getWarningList()
}