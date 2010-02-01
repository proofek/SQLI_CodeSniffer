<?php

class GNNew_Tests_Classes_ClassDeclarationUnitTest extends AbstractSniffUnitTest
{
	
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList($testFile = '')
    {
    	return array(3 => 1);
    }//end getErrorList()
    
    /*protected $_expectedEvents = array(
        'MULTIPLE_CLASS_OR_INTERFACE_IN_SINGLE_FILE' => array(
            array(3, 1),
            array(3, 1),
        )
    );*/
    
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
    }

}