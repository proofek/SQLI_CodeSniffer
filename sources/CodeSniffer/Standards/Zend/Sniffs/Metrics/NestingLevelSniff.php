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
if (class_exists('Generic_Sniffs_Metrics_NestingLevelSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Metrics_NestingLevelSniff not found');
}

/**
 * Zend Framework.
 *
 * Checks the nesting level for methods
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_Metrics_NestingLevelSniff extends Generic_Sniffs_Metrics_NestingLevelSniff
{
    /**
     * A nesting level higher than this value will throw an error.
     *
     * @var integer
     */
    public $absoluteNestingLevel = 5;
}
