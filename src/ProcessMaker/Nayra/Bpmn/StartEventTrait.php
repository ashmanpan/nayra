<?php

namespace ProcessMaker\Nayra\Bpmn;

use ProcessMaker\Nayra\Contracts\Bpmn\CollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\EventDefinitionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\EventInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\FlowNodeInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\TokenInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\TransitionInterface;
use ProcessMaker\Nayra\Contracts\Engine\EngineInterface;
use ProcessMaker\Nayra\Contracts\Engine\ExecutionInstanceInterface;
use ProcessMaker\Nayra\Contracts\RepositoryInterface;

/**
 * Implementation of the behavior of a start event.
 *
 * @package ProcessMaker\Nayra\Bpmn
 */
trait StartEventTrait
{

    use CatchEventTrait;
    /**
     *
     * @var StartTransition
     */
    private $transition;

    /**
     * @var \ProcessMaker\Nayra\Contracts\Bpmn\StateInterface
     */
    private $triggerPlace = [];

    /**
     * Build the transitions of the element.
     *
     * @param RepositoryInterface $factory
     */
    public function buildTransitions(RepositoryInterface $factory)
    {
        $this->setRepository($factory);
        $this->transition = new Transition($this);
        $this->transition->attachEvent(
            TransitionInterface::EVENT_BEFORE_TRANSIT,
            function(TransitionInterface $transition, CollectionInterface $consumeTokens) {
                $this->getRepository()
                    ->getTokenRepository()
                    ->persistStartEventTriggered($this, $consumeTokens);
                $this->notifyEvent(EventInterface::EVENT_EVENT_TRIGGERED, $this, $transition, $consumeTokens);
            }
        );

        $eventDefinitions = $this->getEventDefinitions();
        foreach($eventDefinitions as $index => $eventDefinition) {
            $this->triggerPlace[$index] = new State($this, $eventDefinition->getId());
            $this->triggerPlace[$index]->connectTo($this->transition);
        }
        if ($eventDefinitions->count() === 0) {
            $this->triggerPlace[0] = new State($this);
            $this->triggerPlace[0]->connectTo($this->transition);
        }
    }

    /**
     * Get the input place. Start event does not have an input place.
     *
     * @return null
     */
    public function getInputPlace()
    {
        return null;
    }

    /**
     * Create a flow to a target node.
     *
     * @param \ProcessMaker\Nayra\Contracts\Bpmn\FlowNodeInterface $target
     *
     * @return $this
     */
    protected function buildConnectionTo(FlowNodeInterface $target)
    {
        $this->transition->connectTo($target->getInputPlace());
        return $this;
    }

    /**
     * Start event.
     *
     * @param \ProcessMaker\Nayra\Contracts\Engine\ExecutionInstanceInterface $instance
     *
     * @return $this;
     */
    public function start(ExecutionInstanceInterface $instance)
    {
        $this->triggerPlace[0]->addNewToken($instance);
        return $this;
    }

    /**
     * Method to be called when a message event arrives
     *
     * @param EventDefinitionInterface $event
     * @param ExecutionInstanceInterface|null $instance
     * @param TokenInterface|null $token
     *
     * @return $this
     */
    public function execute(EventDefinitionInterface $event, ExecutionInstanceInterface $instance = null, TokenInterface $token = null)
    {
        foreach ($this->getEventDefinitions() as $index => $eventDefinition) {
            if ($eventDefinition->assertsRule($event, $this, $instance)) {
                if ($instance === null) {
                    $process = $this->getOwnerProcess();
                    $dataStorage = $process->getRepository()->createDataStore();
                    $instance = $process->getEngine()->createExecutionInstance($process, $dataStorage);
                }
                $this->triggerPlace[$index]->addNewToken($instance);
                $eventDefinition->execute($event, $this, $instance, $token);
            }
        }
        return $this;
    }

    /**
     * Register catch events.
     *
     * @param EngineInterface $engine
     *
     * @return $this
     */
    public function registerCatchEvents(EngineInterface $engine)
    {
        foreach ($this->getEventDefinitions() as $eventDefinition) {
            if (is_callable([$eventDefinition, 'registerCatchEvents'])) {
                $eventDefinition->registerCatchEvents($engine, $this, null);
            }
        }
        return $this;
    }
}
