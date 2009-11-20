<?php
if (is_file(dirname(__FILE__).' /../../CodeSniffer/Reports.php')) {
    require_once dirname(__FILE__) . '/../../CodeSniffer/Reports.php';
} else {
    require_once 'SQLI/CodeSniffer/Reports.php';
}


class SQLI_CodeSniffer_ReportsStub extends SQLI_CodeSniffer_Reports 
{
    public function setConfig($filename)
    {
        parent::setEventConfig('Generic', $filename);
    }
    
    public function getEventLevelAndMessage($event)
    {
        $eventLevel = $this->getEventLevel($event);
        $eventMessage = $this->getEventMessage($event);
        
        return array($eventLevel, $eventMessage);
    }
}