<?php
/**
 * Creates the package.xml file needed for distribution.
 * 
 * Use php PackageManager.php to test output on cli, 
 * php PackageManager.php make to really generate package.xml
 */
require_once('PEAR/PackageFileManager2.php');

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagexml = new PEAR_PackageFileManager2();

$e = $packagexml->setOptions(array(
    'clearcontents' => true,
    'baseinstalldir' => 'SQLI',
    'packagedirectory' => '.',
    'dir_roles'         => array(
        'scripts' =>  'script',
        'tests'   => 'test',
        'CodeSniffer/Standards/Generic/Reports' => 'php',
        'CodeSniffer/Standards/GN/Reports' => 'php',
        'CodeSniffer/Standards/Generic/Tests' => 'test',
        'CodeSniffer/Standards/GN/Tests' => 'test',
    ),
    'installexceptions' => array(
        'scripts/sqlics.dist' => '/',
        'scripts/sqlics.bat.dist' => '/',
    )
));

$packagexml->setPackage('SQLI_CodeSniffer');
$packagexml->setSummary('SQLI extension to PHP_CodeSniffer');
$packagexml->setDescription('SQLI_CodeSniffer extends PHP_CodeSniffer to add violation codes and configurable severities and messages');
$packagexml->setUri('http://www.assembla.com/spaces/sqlics/documents');
$packagexml->setAPIVersion('0.3.0dev1');
$packagexml->setReleaseVersion('0.3.0dev1');
$packagexml->setReleaseStability('devel');
$packagexml->setAPIStability('devel');
$packagexml->setNotes("This veresion adds infos to config.xml for Sonar plugin import and  plug and test functionality");
$packagexml->setPackageType('php'); // this is a PEAR-style php script package

$packagexml->setPhpDep('5.1.2');
$packagexml->setPearinstallerDep('1.4.0b1');
$packagexml->addPackageDepWithChannel('package', 'PHP_CodeSniffer', 'pear.php.net', '1.2.0RC1');
$packagexml->addMaintainer('lead', 'blacksun', 'Gabriele Santini', 'gsantini@sqli.com');
$packagexml->setLicense('New BSD License', 'http://www.opensource.org/licenses/bsd-license.php');

$packagexml->addRelease(); // set up a release section
$packagexml->setOSInstallCondition('windows');
$packagexml->addReplacement('scripts/sqlics.bat.dist', 'pear-config', '@php_bin@', 'php_bin');
$packagexml->addReplacement('scripts/sqlics.bat.dist', 'pear-config', '@php_dir@', 'php_dir');
$packagexml->addReplacement('scripts/sqlics.bat.dist', 'pear-config', '@bin_dir@', 'bin_dir');
$packagexml->addInstallAs('scripts/sqlics.bat.dist', 'sqlics.bat');
$packagexml->addInstallAs('scripts/sqlics.dist', 'sqlics');

$packagexml->addRelease(); // set up a release section
$packagexml->addReplacement('scripts/sqlics.dist', 'pear-config', '@php_bin@', 'php_bin');
$packagexml->addInstallAs('scripts/sqlics.dist', 'sqlics');

$packagexml->addGlobalReplacement('package-info', '@package_version@', 'version');
$packagexml->addIgnore('PackageManager.php');
$packagexml->addIgnore('scripts/sqlics');
$packagexml->addIgnore('scripts/sqlics.bat');

$packagexml->generateContents(); // create the <contents> tag




if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}