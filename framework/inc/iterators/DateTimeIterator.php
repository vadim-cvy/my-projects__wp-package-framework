<?php

namespace YOUR_NAMESPACE\framework\inc\iterators;

use \DateTime;
use \DateInterval;

use \Iterator;

if ( ! defined( 'ABSPATH' ) ) exit;

class DateTimeIterator implements Iterator
{
    protected $from;

    protected $to;

    protected $step;

    protected $current;

    public function __construct( DateTime $from, DateTime $to, DateInterval $step = null )
    {
        $this->from = $from;
        $this->to = $to;
        $this->step = $step ? $step : new DateInterval( 'P1D' );
    }

    public function current()
    {
        if ( ! isset( $this->current ) )
        {
            $this->current = $this->from;
        }

        return $this->current;
    }

    public function key()
    {
        return $this->current->getTimestamp();
    }

    public function next() : void
    {
        $this->current->add( $this->step );
    }

    public function rewind() : void
    {
        $this->current = clone $this->from;
    }

    public function valid() : bool
    {
        return $this->current <= $this->to;
    }

}