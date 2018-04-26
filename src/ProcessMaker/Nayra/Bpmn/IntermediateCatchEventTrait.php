<?php

namespace ProcessMaker\Nayra\Bpmn;

use ProcessMaker\Nayra\Bpmn\EndTransition;
use ProcessMaker\Nayra\Bpmn\State;
use ProcessMaker\Nayra\Contracts\Bpmn\EventInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\FlowNodeInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\GatewayInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\IntermediateCatchEventInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\IntermediateThrowEventInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\MessageEventDefinitionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\StateInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\TokenInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\TransitionInterface;
use ProcessMaker\Nayra\Contracts\Repositories\RepositoryFactoryInterface;
use ProcessMaker\Nayra\Exceptions\InvalidSequenceFlowException;

/**
 * End event behavior's implementation.
 *
 * @package ProcessMaker\Nayra\Bpmn
 */
trait IntermediateCatchEventTrait
{

    use FlowNodeTrait;

    /**
     * Receive tokens.
     *
     * @var StateInterface
     */
    private $endState;

    /**
     * Close the tokens.
     *
     * @var EndTransition
     */
    private $transition;

    private $triggerPlace;
    /**
     * Build the transitions that define the element.
     *
     * @param RepositoryFactoryInterface $factory
     */
    public function buildTransitions(RepositoryFactoryInterface $factory)
    {
        $this->setFactory($factory);
        //$this->transition=new ExclusiveGatewayTransition($this);
        $this->transition=new IntermediateCatchEventTransition($this);

        $this->transition->attachEvent(TransitionInterface::EVENT_AFTER_TRANSIT, function()  {
            $this->notifyEvent(IntermediateCatchEventInterface::EVENT_CATCH_TOKEN_PASSED, $this);
        });

        $this->triggerPlace = new  State($this, GatewayInterface::TOKEN_STATE_INCOMMING);
        $this->triggerPlace->connectTo($this->transition);
    }

    /**
     * Get an input to the element.
     *
     * @return StateInterface
     */
    public function getInputPlace()
    {
        $incomingPlace = new State($this, GatewayInterface::TOKEN_STATE_INCOMMING);

        $incomingPlace->connectTo($this->transition);
        $incomingPlace->attachEvent(State::EVENT_TOKEN_ARRIVED, function (TokenInterface $token) {
            $this->notifyEvent(IntermediateCatchEventInterface::EVENT_CATCH_TOKEN_ARRIVES, $this, $token);
        });
        $incomingPlace->attachEvent(State::EVENT_TOKEN_CONSUMED, function (TokenInterface $token) {
            $this->notifyEvent(IntermediateCatchEventInterface::EVENT_CATCH_TOKEN_CONSUMED, $this, $token);
        });
        return $incomingPlace;
    }

    /**
     * Create a connection to a target node.
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
     * To implement the MessageListener interface
     *
     * @param MessageEventDefinitionInterface $message
     */
    public function execute($eventDefinition)
    {
        // with a new token in the trigger place, the event catch element will be fired
        $this->triggerPlace->addNewToken();
    }
}

