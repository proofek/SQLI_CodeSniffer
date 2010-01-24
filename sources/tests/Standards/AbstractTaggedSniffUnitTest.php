<?php

abstract class AbstractTaggedSniffUnitTest extends AbstractSQLISniffUnitTest
{
    // Expected length of an eventDetails array
    const NUMBER_OF_EVENT_DETAILS = 2;
    
    protected $_eventCode;
    protected $_identifier;
    
    /**
     * List of expected events.
     * 
     * Keys are event codes and values arrays of expected line and column.
     * If you have more than one file per event code, put an array
     * 
     * @var array
     */
    protected $_expectedEvents = array();
    
    protected function getEventsDetails()
    {

        if ($this->_identifier) {
            if (!array_key_exists($this->_identifier, $this->_expectedEvents[$this->_eventCode])) {
                return array();
            } else {
                $eventsDetails = $this->_expectedEvents[$this->_eventCode][$this->_identifier];
            }
        } else {
            $eventsDetails = $this->_expectedEvents[$this->_eventCode];
        }
        
        if (self::NUMBER_OF_EVENT_DETAILS === count($eventsDetails) && !is_array($eventsDetails[0])) {
            $eventsDetails = array($eventsDetails);
        }
        
        return $eventsDetails;
    }
    
    protected function setFilenameParts($filename, $sortable)
    {
        $parts = explode('.', $filename);
        if (3 > count($parts) || 4 < count($parts)) {
            throw new Exception("Malformed filename $filename");
        }
        $this->_eventCode = $parts[1];
        if (4 == count($parts)) {
            $this->_identifier = $parts[2];
            if ($sortable) {
                $this->_identifier = $parts[2];
            } else {
                $this->_identifier = $parts[2] - 1;
            }
        }
    }
    
    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return SQLI_CodeSniffer_EventList
     */
    public function getEventList($testFile='')
    {   
        
        $this->setFilenameParts($testFile, $this->isSortable());
        $expectedEventsDetails = $this->getEventsDetails();
        
        $events = array();
        foreach ($expectedEventsDetails as $expectedEvent) {
            $events[] = new SQLI_CodeSniffer_Event($expectedEvent[0], $expectedEvent[1], $this->_eventCode);
        }
        
        return new SQLI_CodeSniffer_EventList($events);         
    }
    
    /**
     * Order test files for parsing.
     * 
     * Default implementation for SortableSniffUnitTest interface.
     * 
     * @param array $testFiles
     * @return void
     */
    public static function sortTestFiles(array &$testFiles)
    {
        sort($testFiles);
    }
}