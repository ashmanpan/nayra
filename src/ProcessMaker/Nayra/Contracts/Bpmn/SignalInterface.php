<?php

namespace ProcessMaker\Nayra\Contracts\Bpmn;

/**
 * Behavior that must implement all event messages
 *
 * @package ProcessMaker\Nayra\Contracts\Bpmn
 */
interface SignalInterface
{
    /**
     * Returns the id of the message
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the id of the message
     * @param string $value
     */
    public function setId(string $value);

    /**
     * Returns the name of the message
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name of the signal
     * @param string $value
     */
    public function setName(string $value);
}