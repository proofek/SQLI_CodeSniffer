<?php
/**
 * PHP_CodeSniffer tokenises PHP code and detects violations of a
 * defined set of coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: CodeSniffer.php,v 1.88 2009/01/05 00:18:20 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

include_once 'PHP/CodeSniffer.php';

/**
 * PHP_CodeSniffer tokenises PHP code and detects violations of a
 * defined set of coding standards.
 *
 * Standards are specified by classes that implement the PHP_CodeSniffer_Sniff
 * interface. A sniff registers what token types it wishes to listen for, then
 * PHP_CodeSniffer encounters that token, the sniff is invoked and passed
 * information about where the token was found in the stack, and the token stack
 * itself.
 *
 * Sniff files and their containing class must be prefixed with Sniff, and
 * have an extension of .php.
 *
 * Multiple PHP_CodeSniffer operations can be performed by re-calling the
 * process function with different parameters.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class SQLI_CodeSniffer extends PHP_CodeSniffer
{

    /**
     * Autoload static method for loading classes and interfaces.
     *
     * @param string $className The name of the class or interface.
     *
     * @return void
     */
    public static function autoload($className)
    {
        if (substr($className, 0, 5) === 'SQLI_') {
            $newClassName = substr($className, 5);
        } else {
            $newClassName = $className;
        }
        
        $path = str_replace('_', '/', $newClassName).'.php';
        
        if (is_file(dirname(__FILE__).'/'.$path)) {
            // Check standard file locations based on class name.
            include dirname(__FILE__).'/'.$path;

        } else if (is_file(dirname(__FILE__).'/CodeSniffer/Standards/'.$path)) {
            // Check for included sniffs in SQLI
            include dirname(__FILE__).'/CodeSniffer/Standards/'.$path;
        } else {
            parent::autoload($className);
        }

    }

    
    /**
     * Processes the files/directories that PHP_CodeSniffer was constructed with.
     * 
     * Duplicated for private _tokenListener declaration
     *
     * @param string|array $files    The files and directories to process. For
     *                               directories, each sub directory will also
     *                               be traversed for source files.
     * @param string       $standard The set of code sniffs we are testing
     *                               against.
     * @param array        $sniffs   The sniff names to restrict the allowed
     *                               listeners to.
     * @param boolean      $local    If true, don't recurse into directories.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If files or standard are invalid.
     */
    public function process($files, $standard, array $sniffs=array(), $local=false)
    {
        if (is_array($files) === false) {
            if (is_string($files) === false || $files === null) {
                throw new PHP_CodeSniffer_Exception('$file must be a string');
            }

            $files = array($files);
        }
        
        if (is_string($standard) === false || $standard === null) {
            throw new PHP_CodeSniffer_Exception('$standard must be a string');
        }

        // Reset the members.
        $this->listeners       = array();
        $this->files           = array();
        $this->_tokenListeners = array(
                                  'file'      => array(),
                                  'multifile' => array(),
                                 );

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo "Registering sniffs in $standard standard... ";
            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo PHP_EOL;
            }
        }

        $this->setTokenListeners($standard, $sniffs);
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $numSniffs = count($this->listeners);
            echo "DONE ($numSniffs sniffs registered)".PHP_EOL;
        }
        
        // Construct a list of listeners indexed by token being listened for.
        foreach ($this->listeners as $listenerClass) {
            
            $listener = new $listenerClass();
            if (($listener instanceof PHP_CodeSniffer_Sniff) === true) {

                $tokens = $listener->register();
                if (is_array($tokens) === false) {
                    $msg = "Sniff $listenerClass register() method must return an array";
                    throw new PHP_CodeSniffer_Exception($msg);
                }

                foreach ($tokens as $token) {
                    if (isset($this->_tokenListeners['file'][$token]) === false) {
                        $this->_tokenListeners['file'][$token] = array();
                    }

                    if (in_array($listener, $this->_tokenListeners['file'][$token], true) === false) {
                        $this->_tokenListeners['file'][$token][] = $listener;
                    }
                }
            } else if (($listener instanceof PHP_CodeSniffer_MultiFileSniff) === true) {
                $this->_tokenListeners['multifile'][] = $listener;
            }
        }//end foreach

        foreach ($files as $file) {
            $this->file = $file;
            if (is_dir($this->file) === true) {
                $this->processFiles($this->file, $local);
            } else {
                $this->processFile($this->file);
            }
        }
        
        // Now process the multi-file sniffs, assuming there are
        // multiple files being sniffed.
        if (count($files) > 1) {
                if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo "process the multi-file sniffs ";
            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo PHP_EOL;
            }
             }
            foreach ($this->_tokenListeners['multifile'] as $listener) {
                // Set the name of the listener for error messages.
                $activeListener = get_class($listener);
                foreach ($this->files as $file) {
                    $file->setActiveListener($activeListener);
                }

                $listener->process($this->files);
            }
        }

    }//end process() 
    

       
    /**
     * Run the code sniffs over each file in a given directory.
     *
     * Duplicated for path reasons
     *
     * @param string  $dir   The directory to process.
     * @param boolean $local If true, only process files in this directory, not
     *                       sub directories.
     *
     * @return void
     * @throws Exception If there was an error opening the directory.
     */
    public function processFiles($dir, $local=false)
    {
        try {
            if ($local === true) {
                $di = new DirectoryIterator($dir);
            } else {
                $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            }

            foreach ($di as $file) {
                $filePath = realpath($file->getPathname());

                if (is_dir($filePath) === true) {
                    continue;
                }

                // Check that the file's extension is one we are checking.
                // Note that because we are doing a whole directory, we
                // are strick about checking the extension and we don't
                // let files with no extension through.
                $fileParts = explode('.', $file);
                $extension = array_pop($fileParts);
                if ($extension === $file) {
                    continue;
                }

                if (isset($this->allowedFileExtensions[$extension]) === false) {
                    continue;
                }

                $this->processFile($filePath);
            }//end foreach
        } catch (Exception $e) {
            $trace = $e->getTrace();

            $filename = $trace[0]['args'][0];
            if (is_numeric($filename) === true) {
                // See if we can find the PHP_CodeSniffer_File object.
                foreach ($trace as $data) {
                    if (isset($data['args'][0]) === true && ($data['args'][0] instanceof PHP_CodeSniffer_File) === true) {
                        $filename = $data['args'][0]->getFilename();
                    }
                }
            }

            $error = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();
            $phpcsFile = new SQLI_CodeSniffer_File($filename, $this->listeners, $this->allowedFileExtensions);
            $this->addFile($phpcsFile);
            $phpcsFile->addEvent('RC_DYNAMIC_ERROR', array('message' => $error ) );
            return;
        }

    }

    /**
     * Run the code sniffs over a single given file.
     *
     * Duplicated for private _tokenListener declaration
     *
     * @param string $file     The file to process.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If the file could not be processed.
     */
    public function processFile($file, $contents=null)
    {
        if ($contents === null && file_exists($file) === false) {
            throw new PHP_CodeSniffer_Exception("Source file $file does not exist");
        }

        // If the file's path matches one of our ignore patterns, skip it.
        foreach ($this->ignorePatterns as $pattern) {
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $file) === 1) {
                return;
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $startTime = time();
            echo 'Processing '.basename($file).' ';
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

        $phpcsFile = new SQLI_CodeSniffer_File(
            $file,
            $this->_tokenListeners['file'],
            $this->allowedFileExtensions
        );
        $this->addFile($phpcsFile);
        $phpcsFile->start($contents);

        // Clean up the test if we can to save memory. This can't be done if
        // we need to leave the files around for multi-file sniffs.
        if (empty($this->_tokenListeners['multifile']) === true) {
            $phpcsFile->cleanUp();
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $timeTaken = (time() - $startTime);
            if ($timeTaken === 0) {
                echo 'DONE in < 1 second';
            } else if ($timeTaken === 1) {
                echo 'DONE in 1 second';
            } else {
                echo "DONE in $timeTaken seconds";
            }

            $events  = $phpcsFile->getEventCount();
            echo " ($events event(s))".PHP_EOL;
        }

    }
    
    /**
     * Gets installed sniffs in the coding standard being used.
     *
     * Duplicated for path reasons.
     *
     * @param string $standard The name of the coding standard we are checking.
     * @param array  $sniffs   The sniff names to restrict the allowed
     *                         listeners to.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If any of the tests failed in the
     *                                   registration process.
     */
    public function getTokenListeners($standard, array $sniffs=array())
    {
        if (is_dir($standard) === true) {
            // This is a custom standard.
            $this->standardDir = $standard;
            $standard          = basename($standard);
        } else {
            $this->standardDir = realpath(dirname(__FILE__).'/CodeSniffer/Standards/'.$standard);
        }
        
        $files = self::getSniffFiles($this->standardDir, $standard);

        if (empty($sniffs) === false) {
            // Convert the allowed sniffs to lower case so
            // they are easier to check.
            foreach ($sniffs as &$sniff) {
                $sniff = strtolower($sniff);
            }
        }

        $listeners = array();

        foreach ($files as $file) {
            // Work out where the position of /StandardName/Sniffs/... is
            // so we can determine what the class will be called.
            $sniffPos = strrpos($file, DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR);
            if ($sniffPos === false) {
                continue;
            }

            $slashPos = strrpos(substr($file, 0, $sniffPos), DIRECTORY_SEPARATOR);
            if ($slashPos === false) {
                continue;
            }

            $className = substr($file, ($slashPos + 1));
            $className = substr($className, 0, -4);
            $className = str_replace(DIRECTORY_SEPARATOR, '_', $className);

            include_once $file;

            // If they have specified a list of sniffs to restrict to, check
            // to see if this sniff is allowed.
            $allowed = in_array(strtolower($className), $sniffs);
            if (empty($sniffs) === false && $allowed === false) {
                continue;
            }

            $listeners[] = $className;

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo "\tRegistered $className".PHP_EOL;
            }
        }//end foreach

        return $listeners;

    }//end getTokenListeners()
    
    /**
     * Determine if a standard is installed.
     *
     * Duplicated for path reasons
     *
     * @param string $standard The name of the coding standard.
     *
     * @return boolean
     * @see getInstalledStandards()
     */
    public static function isInstalledStandard($standard)
    {
        $standardDir  = dirname(__FILE__);
        $standardDir .= '/CodeSniffer/Standards/'.$standard;
        if (is_file("$standardDir/{$standard}CodingStandard.php") === true) {
            return true;
        } else {
            // This could be a custom standard, installed outside our
            // standards directory.
            $standardFile = rtrim($standard, ' /\\').DIRECTORY_SEPARATOR.basename($standard).'CodingStandard.php';
            return (is_file($standardFile) === true);
        }

    }//end isInstalledStandard()

    /**
     * Get a list of all coding standards installed.
     *
     * Duplicated for path reasons.
     *
     * @param boolean $includeGeneric If true, the special "Generic"
     *                                coding standard will be included
     *                                if installed.
     * @param string  $standardsDir   A specific directory to look for standards
     *                                in. If not specified, PHP_CodeSniffer will
     *                                look in its default location.
     *
     * @return array
     * @see isInstalledStandard()
     */
    public static function getInstalledStandards(
        $includeGeneric=false,
        $standardsDir=''
    ) {
        $installedStandards = array();

        if ($standardsDir === '') {
            $standardsDir = dirname(__FILE__).'/CodeSniffer/Standards';
        }

        $di = new DirectoryIterator($standardsDir);
        foreach ($di as $file) {
            if ($file->isDir() === true && $file->isDot() === false) {
                $filename = $file->getFilename();

                // Ignore the special "Generic" standard.
                if ($includeGeneric === false && $filename === 'Generic') {
                    continue;
                }

                // Valid coding standard dirs include a standard class.
                $csFile = $file->getPathname()."/{$filename}CodingStandard.php";
                if (is_file($csFile) === true) {
                    // We found a coding standard directory.
                    $installedStandards[] = $filename;
                }
            }
        }

        return $installedStandards;

    }//end getInstalledStandards()
    /**
     * Return a list of sniffs that a coding standard has defined.
     *
     * Duplicated for path reasons.
     *
     * @param string $dir      The directory where to look for the files.
     * @param string $standard The name of the coding standard. If NULL, no
     *                         included sniffs will be checked for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If an included or excluded sniff does
     *                                   not exist.
     */
    public static function getSniffFiles($dir, $standard=null)
    {
        $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $ownSniffs      = array();
        $includedSniffs = array();
        $excludedSniffs = array();

        foreach ($di as $file) {
            // Skip hidden files.
            if (substr($file->getFilename(), 0, 1) === '.') {
                continue;
            }

            // We are only interested in PHP and sniff files.
            $fileParts = explode('.', $file);
            if (array_pop($fileParts) !== 'php') {
                continue;
            }

            $basename = basename($file, '.php');
            if (substr($basename, -5) !== 'Sniff') {
                continue;
            }

            $ownSniffs[] = $file->getPathname();
        }//end foreach

        // Load the standard class and ask it for a list of external
        // sniffs to include in the standard.
        if ($standard !== null
            && is_file("$dir/{$standard}CodingStandard.php") === true
        ) {
            include_once "$dir/{$standard}CodingStandard.php";
            $standardClassName = "PHP_CodeSniffer_Standards_{$standard}_{$standard}CodingStandard";
            $standardClass     = new $standardClassName;

            $included = $standardClass->getIncludedSniffs();
            foreach ($included as $sniff) {
                if (is_dir($sniff) === true) {
                    // Trying to include from a custom standard.
                    $sniffDir = $sniff;
                    $sniff    = basename($sniff);
                } else if (is_file($sniff) === true) {
                    // Trying to include a custom sniff.
                    $sniffDir = $sniff;
                } else {
                    $sniffDir = realpath(dirname(__FILE__)."/CodeSniffer/Standards/$sniff");
                    if (is_dir($sniffDir) === false) {
                        // try to recover PHP_CodeSniffer Sniffer
                        $sniffDir = "PHP/CodeSniffer/Standards/$sniff";
//                        var_dump($sniff);exit;
                    }
                }

                if (is_dir($sniffDir) === true) {
                    if (self::isInstalledStandard($sniff) === true) {
                        // We are including a whole coding standard.
                        $includedSniffs = array_merge($includedSniffs, self::getSniffFiles($sniffDir, $sniff));
                    } else {
                        // We are including a whole directory of sniffs.
                        $includedSniffs = array_merge($includedSniffs, self::getSniffFiles($sniffDir));
                    }
                } else {
                    if (substr($sniffDir, -9) !== 'Sniff.php') {
                        $error = "Included sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }

                    $includedSniffs[] = $sniffDir;
                }
            }//end foreach

            $excluded = $standardClass->getExcludedSniffs();
            foreach ($excluded as $sniff) {
                if (is_dir($sniff) === true) {
                    // Trying to exclude from a custom standard.
                    $sniffDir = $sniff;
                    $sniff    = basename($sniff);
                } else if (is_file($sniff) === true) {
                    // Trying to exclude a custom sniff.
                    $sniffDir = $sniff;
                } else {
                    $sniffDir = realpath(dirname(__FILE__)."/CodeSniffer/Standards/$sniff");
                    if ($sniffDir === false) {
                        $error = "Excluded sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }
                }

                if (is_dir($sniffDir) === true) {
                    if (self::isInstalledStandard($sniff) === true) {
                        // We are excluding a whole coding standard.
                        $excludedSniffs = array_merge(
                            $excludedSniffs,
                            self::getSniffFiles($sniffDir, $sniff)
                        );
                    } else {
                        // We are excluding a whole directory of sniffs.
                        $excludedSniffs = array_merge(
                            $excludedSniffs,
                            self::getSniffFiles($sniffDir)
                        );
                    }
                } else {
                    if (substr($sniffDir, -9) !== 'Sniff.php') {
                        $error = "Excluded sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }

                    $excludedSniffs[] = $sniffDir;
                }
            }//end foreach
        }//end if

        // Merge our own sniff list with our externally included
        // sniff list, but filter out any excluded sniffs.
        $files = array();
        foreach (array_merge($ownSniffs, $includedSniffs) as $sniff) {
            if (in_array($sniff, $excludedSniffs) === true) {
                continue;
            } else {
                $files[] = $sniff;
            }
        }
        
        return $files;

    }
    
    /**
     * Gives Events collected for reports
     *
     * @return array
     */
    public function getFilesEvents()
    {
        $files = array();
        
        foreach ($this->files as $file) {
            $events    = $file->getEventList();
            $filename    = $file->getFilename();
            $files[$filename] = $events;
        }
        
        return $files;

    }
    
}

spl_autoload_unregister(array('PHP_CodeSniffer', 'autoload'));
spl_autoload_register(array('SQLI_CodeSniffer', 'autoload'));
?>
