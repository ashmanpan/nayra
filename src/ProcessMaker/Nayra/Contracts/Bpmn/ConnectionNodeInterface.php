<?php

namespace ProcessMaker\Nayra\Contracts\Bpmn;

use ProcessMaker\Nayra\Bpmn\Collection;

/**
 * Connection node (States and transitions) that define the behavior of
 * a bpmn flow node.
 *
 * @package ProcessMaker\Nayra\Contracts\Bpmn
 */
interface ConnectionNodeInterface
{

    /**
     * @return Collection Outgoing flows.
     */
    public function outgoing();

    /**
     * @return Collection Incoming flows.
     */
    public function incoming();

    /**
     * Add an outgoing flow.
     *
     * @param \ProcessMaker\Nayra\Contracts\Bpmn\ConnectionNodeInterface $target
     */
    public function connectTo(ConnectionNodeInterface $target);
}