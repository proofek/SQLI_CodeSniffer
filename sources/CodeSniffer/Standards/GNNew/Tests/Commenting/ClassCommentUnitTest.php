<?php

class GNNew_Tests_Commenting_ClassCommentUnitTest extends AbstractSniffUnitTest
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
        	case 'ClassCommentUnitTest.BLANK_LINE_BEFORE_PARAM_CLASS_COMMENT.inc':
        		return array(6 => 1);
        		break;
        	case 'ClassCommentUnitTest.BLANK_LINE_BETWEEN_CLASS_COMMENT.inc':
        		return array(6 => 1);
        		break;
        	case 'ClassCommentUnitTest.BLANK_LINE_CLASS_COMMENT.inc':
        		return array(2 => 1);
        		break;
        	case 'ClassCommentUnitTest.CLASS_COMMENT_EMPTY.inc':
        		return array(4 => 1);
        		break;
        	case 'ClassCommentUnitTest.EXTRA_LINE_CLASS_COMMENT.inc':
        		return array(5 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_AUTHOR_TAG_CLASS_COMMENT.inc':
        		return array(14 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_CATEGORY_TAG_CLASS_COMMENT.inc':
        		return array(14 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_CLASS_COMMENT.inc':
        		return array(2 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_PACKAGE_TAG_CLASS_COMMENT.inc':
        		return array(14 => 1);
        		break;		
        	case 'ClassCommentUnitTest.MISSING_SHORT_DESC_CLASS_COMMENT.inc':
        		return array(4 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_SUBPACKAGE_TAG_CLASS_COMMENT.inc':
        		return array(14 => 1);
        		break;
        	case 'ClassCommentUnitTest.MISSING_VERSION_TAG_CLASS_COMMENT.inc':
        		return array(14 => 1);
        		break;
        	case 'ClassCommentUnitTest.ONE_VERSION_TAG_CLASS_COMMENT.inc':
        		return array(15 => 1);
        		break;
        	case 'ClassCommentUnitTest.SHORT_DESC_CLASS_COMMENT.inc':
        		return array(5 => 1);
        		break;
        	case 'ClassCommentUnitTest.STYLE_CLASS_COMMENT.inc':
        		return array(9 => 1);
        		break;
        	case 'ClassCommentUnitTest.SUBVERSION_KEYWORD_AUTHOR_TAG_CLASS_COMMENT.inc':
        		return array(15 => 1);
        		break;
        	case 'ClassCommentUnitTest.SUBVERSION_KEYWORD_VERSION_TAG_CLASS_COMMENT.inc':
        		return array(13 => 1);
        		break;
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
        	case 'ClassCommentUnitTest.TAG_NOTALLOWED_CLASS_COMMENT.inc':
        		return array(7 => 1);
        		break;
        	default:
	            return array();
	            break;
        }       
    }//end getWarningList()
	
}