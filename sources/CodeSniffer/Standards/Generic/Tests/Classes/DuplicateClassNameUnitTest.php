<?php
/**
 * Unit test class for the DuplicateClassName multi-file sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: DuplicateClassNameUnitTest.php,v 1.1 2008/07/25 04:24:10 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the DuplicateClassName multi-file sniff.
 *
 * A multi-file sniff unit test checks a .1.inc and a .2.inc file for expected violations
 * of a single coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Tests_Classes_DuplicateClassNameUnitTest extends AbstractTaggedSniffUnitTest implements SortableSniffUnitTest
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
        'DUPLICATE_CLASS_OR_INTERFACE' => array(
            '1' => array(array(6,1), array(7,1)),
            '2' => array(array(2,1), array(3,1)),
        ),
    );

}