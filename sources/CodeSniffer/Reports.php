<?php
class SQLI_CodeSniffer_Reports
{
    const EMERGENCY = 0;  // Emergency: system will be unusable
    const ALERT     = 1;  // Alert: action must be taken immediately
    const CRITICAL  = 2;  // Critical: critical conditions
    const ERROR     = 3;  // Error: error conditions
    const WARNING   = 4;  // Warning: warning conditions
    const NOTICE    = 5;  // Notice: normal but significant condition
    const INFO      = 6;  // Informational: informational messages
    const DEBUG     = 7;  // Debug: debug messages
    
    /**
     * Parsed standard config values.
     * 
     * Indexed by event code.
     *
     * @var array
     */
    protected $configArray = array();
    
    /**
     * Produce the appropriate report object based on $type parameter
     *
     * @param string $type
     * 
     * @return SQLI_CodeSniffer_Reports_Abstract
     * 
     * @throws SQLI_CodeSniffer_Exception if the object can't be instanciated
     */
    public function factory($type)
    {
        $type = ucfirst($type);
        $filename = $type . '.php';

        $reportClassName = 'SQLI_CodeSniffer_Reports_' . $type;
        try {
            return new $reportClassName();
        } catch (Exception $e) {
            throw new SQLI_CodeSniffer_Exception("Class '$reportClassName' not found in file $filename");
        }
        
    }
    
    /**
     * Reads the standard config files and stores its infos in $configArray
     *
     * @param string $standard
     * @param string $filename
     * 
     * @return void
     */
    protected function setEventConfig($standard = 'PEAR', $filename = '') 
    {
        if (!$filename) {
            $filename = dirname(__FILE__) . '/Standards/' . $standard . '/Reports/config.xml';
        }
        $config = simplexml_load_file($filename);
        
        $this->configArray = array();
        foreach ($config as $event) {
            $code = (string) $event['code'];
            $this->configArray[$code] = array(
                'message' => (string) $event->message,
                'level'   => (string) $event->level
            );     
        }
    }
    
    /**
     * Gives the priority integer for a given level.
     *
     * @param string $level
     * 
     * @return int
     */
    public static function getLevelPriority($level)
    {
        return constant('self::' . $level);
    }
    
    /**
     * Verifies that event code exists or throw an exception
     *
     * @param string $code
     * 
     * @return bool
     */
    protected function isConfiguredEventCode($code)
    {
        return isset($this->configArray[$code]);     
    }
    
    /**
     * Gives the level name for a given Event.
     *
     * @param SQLI_CodeSniffer_Event $event
     * 
     * @return string
     */
    protected function getEventLevel($event)
    {   
        $event_code = $event->getCode();
        if ($this->isConfiguredEventCode($event_code)) {
            return $this->configArray[$event_code]['level'];
        } else {
            return $event->getLevel();
        }
    }
    
    /**
     * Gives the untreated config message for a given Event.
     *
     * @param SQLI_CodeSniffer_Event $event
     * 
     * @return string
     */
    protected function getEventMessage($event)
    {
        $event_code = $event->getCode();
        if ($this->isConfiguredEventCode($event_code)) {
            return $this->configArray[$event_code]['message'];
        } else {
            return $event->getMessage();
        }
    }
    
    /**
     * Fills EventList and Event Objects with infos taken by config files.
     * 
     * Declares EventList and Event as "report ready".
     *
     * @param SQLI_CodeSniffer_EventList $events
     */
    protected function setEventsInfos(SQLI_CodeSniffer_EventList &$events)
    {
        foreach ($events as $event) {
            if ($this->isConfiguredEventCode($event->getCode())) {
                $eventLevel = $this->getEventLevel($event);
                $eventMessage = $this->getEventMessage($event);
                $event->setReportInfos($eventMessage, $eventLevel);                
            } else {
                $eventLevel = $this->getEventLevel($event);
            }

            $events->addLevelCount($eventLevel);
        }
        
        $events->setReportReady();
    }
    
    /**
     * Gives an error array ready for reports.
     *
     * Cuts out events lighter than $showLevel.
     * 
     * @param SQLI_CodeSniffer_EventList $events
     * @param int                        $showLevel
     * 
     * @return array
     */
    protected function getErrors(SQLI_CodeSniffer_EventList $events, $showLevel)
    {
        $errors = array();
        foreach ($events as $event) {
            $eventLevel   = $event->getLevel();
            if ($showLevel >= self::getLevelPriority($eventLevel)) {
                $newError = array(
                    'code'    => $event->getCode(),
                    'message' => $event->getMessage(),
                    'source'  => $event->getSource(),
                    'type'    => strtolower($this->getEventLevel($event))
                );
                $errors[$event->getLine()][$event->getColumn()][] = $newError;
            }
        }

        return $errors;
    }
    
    /**
     * Pre-process and package events for all files.
     *
     * Used by error reports to get a packaged list of all errors in each file.
     *
     * @param array  $filesEvents Show warnings as well as errors.
     * @param string $standard    Standard to use.
     * @param int    $showLevel   Cut level for events reports.
     *
     * @return array
     */
    public function prepareErrorReport(array $filesEvents, $standard, $showLevel=SQLI_CodeSniffer_Report::WARNING)
    {
        $report = array(
            'totals' => array(),
            'files'  => array(),
        );
        
        $this->setEventConfig($standard);
        
        foreach ($filesEvents as $filename => &$events) {
            
            $this->setEventsInfos($events);
            if ($events->getStrongerLevelCounts($showLevel) == 0) {
                // Perfect score for this level
                continue;
            }
            
            foreach ($events->getLevelCounts() as $level => $count) {
                $level_name = strtolower($level);
                $report['files'][$filename]['events'][$level_name] = $count;
                
                if (!isset ($report['totals'][$level_name])) {
                    $report['totals'][$level_name] = $count;
                } else {
                    $report['totals'][$level_name] += $count;
                }
            }
            $errors = $this->getErrors($events, $showLevel);
            ksort($errors);

            $report['files'][$filename]['messages'] = $errors;
        }

        return $report;

    }   
}