<?php

class GN_Tests_Classes_ClassDeclarationUnitTest extends AbstractTaggedSniffUnitTest
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
        'MULTIPLE_CLASS_OR_INTERFACE_IN_SINGLE_FILE' => array(
            array(3, 1),
            array(3, 1),
        )
    );

}