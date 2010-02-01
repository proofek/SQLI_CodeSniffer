<?php

class GNNew_Tests_Classes_ClassFileNameUnitTest extends AbstractSniffUnitTest
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
    	return array();
    }//end getErrorList()
    
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