<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

if (class_exists('Generic_Sniffs_Files_LineLengthSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Files_LineLengthSniff not found');
}

/**
 * Zend_Sniffs_Files_LineLengthSniff
 *
 * Checks all lines in the file, and throws warnings if they are over 100
 * characters in length and errors if they are over 120.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class GN_Sniffs_Files_LineLengthSniff extends Zend_Sniffs_Files_LineLengthSniff
{
    /**
     * The limit that the length of a line should not exceed
     *
     * @var integer
     */
    public $lineLimit = 110;

    /**
     * The limit that the length of a line must not exceed
     *
     * Set to zero (0) to disable
     *
     * @var integer
     */
    public $absoluteLineLimit = 110;

}
