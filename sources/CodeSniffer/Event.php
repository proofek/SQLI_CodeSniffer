<?php
/**
 * Events raised by sniffers.
 * 
 * Events are created at analyse time and carry all detection-relative infos.
 * They are uniquely identified by a code.
 * They are completed at report time with data coming from external configuration.
 *
 */
class SQLI_CodeSniffer_Event
{
    const RAISED       = 0;
    const REPORT_READY = 1;

    /**
     * Status of Event.
     * 
     * Initialised to just "raised"
     *
     * @var int
     */
    protected $_status = self::RAISED;
    
    /**
     * Line of Event detection
     *
     * @var int
     */
    protected $_line = 1;

    /**
     * Column of Event detection
     *
     * @var int
     */
    protected $_column = 1;

    /**
     * Event unique code
     *
     * @var string
     */
    protected $_code;

    /**
     * Event detection sniffer
     *
     * @var string
     */
    protected $_source;
    
    /**
     * Dynamic parameters for delayed message instanciation
     *
     * @var array
     */
    protected $_parameters = array();   
    
    /**
     * Event message.
     * 
     * Filled at report time.
     *
     * @var unknown_type
     */
    protected $_message;

    /**
     * Event severity level.
     * 
     * Filled at report time.
     *
     * @var unknown_type
     */
    protected $_level;    
    
    /**
     * Creates an Event.
     *
     * @param int    $line
     * @param int    $column
     * @param string $code
     * @param array  $parameters
     * @param string $listener
     * 
     * @throws SQLI_CodeSniffer_Exception wrong parameters
     */
    public function __construct($line, $column, $code, $parameters = array(), $listener = '')
    {
        if (!is_int($line) || 1 > $line) {
            throw new SQLI_CodeSniffer_Exception("Invalid line specified in Event creation");
        }
        if (!is_int($column) || 1 > $column) {
            throw new SQLI_CodeSniffer_Exception("Invalid column specified in Event creation");
        }
        if (!is_string($code) || '' === $code) {
            throw new SQLI_CodeSniffer_Exception("Invalid code specified in Event creation");
        }
        if ($listener) {
            // Work out which sniff generated the event.
            // TODO : add a _sniffer property and convert to a source based on report type !
            $parts = explode('_', $listener);
            // check if the listener is a SQLI kind
            $reflectionListener= new ReflectionClass($listener);
            if ($reflectionListener->implementsInterface('SQLI_CodeSniffer_Sniff')) {
              $this->_source = $parts[0] . '/' . $parts[2] . '/' . $parts[3] . '/' . $code;  
            } else {
              $this->_source = 'PHPCS/Generic';
              // $this->_source = $parts[0].'.'.$parts[2].'.'.$parts[3];
            }           
        }
        $this->_line       = $line;
        $this->_column     = $column;
        $this->_code       = $code;
        $this->_parameters = $parameters;
    }
    
    /**
     * Gives the line of detection.
     *
     * @return unknown
     */
    public function getLine()
    {
        return $this->_line;
    }
    
    /**
     * Gives the column of detection.
     *
     * @return unknown
     */
    public function getColumn()
    {
        return $this->_column;
    }
    
    /**
     * Gives the event code identifier.
     *
     * @return unknown
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * Gives the detection Sniffer
     *
     * @return unknown
     */
    public function getSource()
    {
        return $this->_source;
    }
    
    /**
     * Gives dynamic parameters
     *
     * @return unknown
     */
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    /**
     * Makes the Event status evolve to "report ready"
     */
    protected function setReportReady()
    {
        $this->_status = self::REPORT_READY;
    }
    
    /**
     * Tells if the Event is in "report ready" status
     *
     * @return boolean
     */
    public function isReportReady()
    {
        return $this->_status == self::REPORT_READY;
    }
    
    /**
     * Gives the Event severity level
     *
     * @return unknown
     */
    public function getLevel()
    {
        if (!$this->isReportReady()) {
            throw new SQLI_CodeSniffer_Exception(sprintf("Event '%s' is not ready for report", $this->getCode()));
        }
        return $this->_level;
    }
    
    /**
     * Gives Event message.
     *
     * @return string
     */
    public function getMessage()
    {
        if (!$this->isReportReady()) {
            throw new SQLI_CodeSniffer_Exception("Event is not ready for report");
        }
        return $this->_message;
    }

    /**
     * Set infos necessary for report.
     * 
     * Set the event status to "report ready".
     *
     * @param string $message
     * @param string $level
     */
    public function setReportInfos($message, $level)
    {
        $this->setMessage($message);
        $this->setLevel($level);
        $this->setReportReady();
    }    
    
    /**
     * Set Event message.
     * 
     * Replace placeholders with parameters
     *
     * @param string $message
     */
    protected function setMessage($message)
    {
        if (!empty($this->_parameters)) {
            foreach($this->_parameters as $key => $value) {
                $message = str_replace(":$key", $value, $message);
            }
        }
        $this->_message = $message;
    }
    
    /**
     * Set severity level.
     *
     * @param string $level
     */
    protected function setLevel($level)
    {
        $this->_level = $level;
    }
}