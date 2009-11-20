<?php

class GN_Tests_Commenting_ClassCommentUnitTest extends AbstractTaggedSniffUnitTest
{   
    /**
     * List of expected events.
     * 
     * Keys are event codes and values arrays of expected line and column.
     * If you have more than one file per event code, put an array
     * 
     * @var array
     */
    protected $_expectedEvents = array(
        'MISSING_CLASS_COMMENT'                        => array(2, 1),
        'STYLE_CLASS_COMMENT'                          => array(9, 1),
        'BLANK_LINE_CLASS_COMMENT'                     => array(2, 1),
        'CLASS_COMMENT_EMPTY'                          => array(4, 1),
        'TAG_NOTALLOWED_CLASS_COMMENT'                 => array(7, 1),
        'MISSING_CATEGORY_TAG_CLASS_COMMENT'           => array(14, 1),
        'MISSING_PACKAGE_TAG_CLASS_COMMENT'            => array(14, 1),
        'MISSING_SUBPACKAGE_TAG_CLASS_COMMENT'         => array(14, 1),
        'MISSING_AUTHOR_TAG_CLASS_COMMENT'             => array(14, 1),
        'MISSING_VERSION_TAG_CLASS_COMMENT'            => array(14, 1),
        'ONE_VERSION_TAG_CLASS_COMMENT'                => array(15, 1),
        'SUBVERSION_KEYWORD_AUTHOR_TAG_CLASS_COMMENT'  => array(15, 1),
        'SUBVERSION_KEYWORD_VERSION_TAG_CLASS_COMMENT' => array(13, 1),
        'MISSING_SHORT_DESC_CLASS_COMMENT'             => array(4, 1),
        'BLANK_LINE_BEFORE_PARAM_CLASS_COMMENT'        => array(6, 1),
        'SHORT_DESC_CLASS_COMMENT'                     => array(5, 1),
        'EXTRA_LINE_CLASS_COMMENT'                     => array(5, 1),
        'BLANK_LINE_BETWEEN_CLASS_COMMENT'             => array(6, 1),
        // TODO : Missing tests     
        //MISSING_CONTENT_AUTHOR_TAG_CLASS_COMMENT              
        //MISSING_CONTENT_VERSION_TAG_CLASS_COMMENT     
    );

}