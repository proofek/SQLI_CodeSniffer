<?php
class ZF_Tests_Classes_ClassDeclarationUnitTest extends AbstractSniffUnitTest
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
	        case 'ClassDeclarationUnitTest.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.2.inc':
	        	return array(4 => 1);
	        case 'ClassDeclarationUnitTest.3.inc':
	        	return array(3 => 1);	
	        case 'ClassDeclarationUnitTest.4.inc':
	        	return array(3 => 1);
	       	case 'ClassDeclarationUnitTest.5.inc':
	        	return array(7 => 1);
	        case 'ClassDeclarationUnitTest.6.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.7.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.8.inc':
	        	return array(5 => 1);
	        case 'ClassDeclarationUnitTest.9.inc':
	        	return array(4=> 1);
	        case 'ClassDeclarationUnitTest.12.inc':
	        	return array(4 => 1);
	        case 'ClassDeclarationUnitTest.14.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.15.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.17.inc':
	        	return array(2 => 1);
	        case 'ClassDeclarationUnitTest.18.inc':
	        	return array(2 => 1);				
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
    public function getWarningList()
    {
        return array();

    }//end getWarningList()

}//end class
?>