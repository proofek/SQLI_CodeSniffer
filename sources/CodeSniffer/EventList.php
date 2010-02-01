<?php
/**
 * Mantains a list of SQLI_CodeSniffer_Events
 *
 */
class SQLI_CodeSniffer_EventList implements Iterator, Countable 
{
    
    const RAISED       = 0;
    const REPORT_READY = 1;
    
    /**
     * Status of EventList.
     * 
     * Initialised to just "raised"
     *
     * @var int
     */
    protected $_status = self::RAISED;
    
    /**
     * Pointer for the Iterator Interface
     *
     * @var array
     */
    protected $_iteratorPointer = array();
    
    /**
     * Events raised from PHP_CodeSniffer_Sniffs.
     * 
     * The format is an array of array of arrays:
     *   - the first array indexes are detection lines,
     *   - the second array indexes are detection columns,
     *   - the final array contains a list of SQLI_CodeSniffer_Event. 
     *
     * @var array()
     */
    protected $_events = array();

    /**
     * The total number of raised events.
     *
     * @var int
     */
    protected $_eventCount = 0;
    
    /**
     * The number of raised events by level. 
     *
     * @var array
     */
    protected $_countByLevel = array();


    public function __construct(array $eventArray = array())
    {
        if (count($eventArray)) {       
            foreach ($eventArray as $event) {
                $this->addEvent($event);
            }
            
            $this->rewind();
        }
    }
    
    /**
     * Add an event to the event list.
     * 
     * @param SQLI_CodeSniffer_Event
     * @return void
     */
    public function addEvent(SQLI_CodeSniffer_Event $event)
    {
        $line = $event->getLine();
        $column = $event->getColumn();
        if (isset($this->_events[$line]) === false) {
            $this->_events[$line] = array();
        }
        
        if (isset($this->_events[$line][$column]) === false) {
            $this->_events[$line][$column] = array();
        }

        $this->_events[$line][$column][] = $event;
        $this->_eventCount++;
    }
    
    /**
     * Gives the total number of events in this list.
     *
     * @return unknown
     */
    public function count()
    {
        return $this->_eventCount;
    }
    
    /**
     * Tells if EventList has this event.
     * 
     * Equality is based on line, column and code
     *
     * @param SQLI_CodeSniffer_Event $event
     */
    public function hasEvent(SQLI_CodeSniffer_Event $event)
    {
        $lineColumnEvents = $this->getEvents($event->getLine(), $event->getColumn());
        if (!empty($lineColumnEvents)) {
            foreach ($lineColumnEvents as $lineColumnEvent) {
                if ($lineColumnEvent->getCode() == $event->getCode()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Returns the events raised at corresponding line and column
     *
     * @param int $line
     * @param int $column
     * @return array
     */
    public function getEvents($line, $column = null)
    {
        if (!isset($this->_events[$line])) {
            return $this;
        }
        if ($column) {
            if (!isset($this->_events[$line][$column])) {
                return array();
            }
            $result = $this->_events[$line][$column]; 
        } else {
            $result = $this->_events[$line];
        }
        
        return $result;
    }
    
    /**
     * Makes the EventList status evolve to "report ready"
     */
    public function setReportReady()
    {
        $this->_status = self::REPORT_READY;
    }
    
    /**
     * Tells if the EventList is in "report ready" status
     *
     * @return boolean
     */
    public function isReportReady()
    {
        return $this->_status == self::REPORT_READY;
    }
    
    /**
     * Adds one to a level count.
     *
     * @param string $level
     */
    public function addLevelCount($level)
    {
        if (!isset($this->_countByLevel[$level])) {
            $this->_countByLevel[$level] = 1;
        } else {
            $this->_countByLevel[$level]++;
        }
    }
    
    /**
     * Gives the number of events for each level
     *
     * @param string $level
     * 
     * @return array
     */
    public function getLevelCounts($level = null)
    {
        if (!$this->isReportReady()) {
            throw new SQLI_CodeSniffer_Exception("EventList is not ready for report");
        }
        
        if ($level) {
            if (isset($this->_countByLevel[$level])) {
                return $this->_countByLevel[$level];
            } else {
                return 0;
            }
        } else {
            return $this->_countByLevel;
        }
    }
    
    /**
     * Gives the number of events of at least the $cutLevel level
     *
     * @param string $cutLevel
     * 
     * @return int
     */
    public function getStrongerLevelCounts($cutLevel)
    {
        if (!$this->isReportReady()) {
            throw new SQLI_CodeSniffer_Exception("EventList is not ready for report");
        }
        
        $sum = 0;
        foreach ($this->_countByLevel as $level => $count) {
            if (SQLI_CodeSniffer_Reports::getLevelPriority($level) <= $cutLevel) {
                $sum += $count;
            }
        }
        
        return $sum;
    }
    
    /**
     * Sort EventList representation according to line and column.
     *
     * @return void
     */
    protected function sort()
    {
        foreach ($this->_events as &$lineEvents) {
            ksort($lineEvents);
        }
        ksort($this->_events);  
    }
    
   /**
    * Rewind the Iterator to the first element.
    * 
    * @return void
    */
    public function rewind()
    {
        if (!empty($this->_events)) {
            $this->sort();
            $columnEvents = reset($this->_events);
            reset($columnEvents);

            $this->_iteratorPointer = array( 
              'line'    => key($this->_events), 
              'column'  => key($columnEvents), 
              'element' => 0
            );            
        }
    }
   
   /**
    * Return the current element.
    * 
    * Similar to the current() function for arrays in PHP
    * 
    * @return SQLI_CodeSniffer_Element current element from the collection
    */
    public function current()
    {
       return ($this->_events
           [$this->_iteratorPointer['line']]
           [$this->_iteratorPointer['column']]
           [$this->_iteratorPointer['element']]
       );
    }

   /**
    * Throws an exception as EventList doesn't work on keys.
    * 
    * @return mixed either an integer or a string
    * @throws SQLI_CodeSniffer_Exception
    */
    public function key()
    {
       throw new SQLI_CodeSniffer_Exception("Can't recover key from an EventList element");
    }

   /**
    * Move forward to next element.
    * 
    * @return void
    */
    public function next()
    {
       $elementArray = $this->_events[$this->_iteratorPointer['line']][$this->_iteratorPointer['column']];
       end($elementArray);
       
       if (key($elementArray) > $this->_iteratorPointer['element']) {
           // we are within last array foreach
           $this->_iteratorPointer['element']++;
       } else {
           $columnArray = $this->_events[$this->_iteratorPointer['line']];
           end($columnArray);
           if (key($columnArray) > $this->_iteratorPointer['column'])
           {
               // we are inside column foreach: take next column
               reset($columnArray);
               while (key($columnArray) <= $this->_iteratorPointer['column']) {
                   next($columnArray);
               }
               $this->_iteratorPointer['column'] = key($columnArray);
               $this->_iteratorPointer['element'] = 0;
           } else {
               // we are at the end of column foreach: take next line
               end($this->_events);
               if (key($this->_events) == $this->_iteratorPointer['line']) {
                   // we already are at the end of first array
                   // make sure next call is not valid
                   $this->_iteratorPointer['line']++;
               } else {
                   reset($this->_events);
                   while (key($this->_events) <= $this->_iteratorPointer['line']) {
                       next($this->_events);
                   }
                   $this->_iteratorPointer['line'] = key($this->_events);
                   $columnArray = $this->_events[$this->_iteratorPointer['line']];
                   reset($columnArray);
                   $this->_iteratorPointer['column'] = key($columnArray);
                   $this->_iteratorPointer['element'] = 0;
               }
           }
       }
    }
   
   /**
    * Check if there is a current element after calls to rewind() or next().
    * 
    * Used to check if we've iterated to the end of the collection
    * 
    * @return boolean FALSE if there's nothing more to iterate over
    */
    public function valid()
    {
        return (
            isset($this->_iteratorPointer['line'])
            && isset($this->_events[$this->_iteratorPointer['line']])
            && isset($this->_events[$this->_iteratorPointer['line']][$this->_iteratorPointer['column']])
            && isset($this->_events[$this->_iteratorPointer['line']][$this->_iteratorPointer['column']][$this->_iteratorPointer['element']])
        );
    }
}