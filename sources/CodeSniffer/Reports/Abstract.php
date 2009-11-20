<?php
abstract class SQLI_CodeSniffer_Reports_Abstract
{
    public abstract function generate($report, $showSources=false);
}