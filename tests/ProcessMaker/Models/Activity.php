<?php

namespace ProcessMaker\Models;

use ProcessMaker\Nayra\Bpmn\ActivityTrait;
use ProcessMaker\Nayra\Bpmn\ActivityWorkException;
use ProcessMaker\Nayra\Contracts\Bpmn\ActivityInterface;

/**
 * Activity implementation.
 *
 * @package ProcessMaker\Models
 */
class Activity implements ActivityInterface
{
    use ActivityTrait,
        LocalFlowNodeTrait,
        LocalProcessTrait,
        LocalPropertiesTrait;

    /**
     * Called when activated.
     *
     * @throws ActivityWorkException
     */
    public function work()
    {

    }

    /**
     * Array map of custom event classes for the bpmn element.
     *
     * @return array
     */
    protected function getBpmnEventClasses()
    {
        return [
            ActivityInterface::EVENT_ACTIVITY_ACTIVATED => ActivityActivatedEvent::class,
        ];
    }
}