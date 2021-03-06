<?php

namespace ProcessMaker\Nayra\Bpmn;

use PHPUnit\Framework\TestCase;
use ProcessMaker\Nayra\Bpmn\Models\Process;
use ProcessMaker\Nayra\Contracts\Bpmn\ActivityCollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\ArtifactCollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\DataStoreCollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\EventCollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\FlowCollectionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\GatewayCollectionInterface;

class ProcessTraitTest extends TestCase
{
    /**
     * @var ProcessTrait $object
     */
    private $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new Process();
    }

    public function testCollections()
    {
        $this->assertInstanceOf(ActivityCollectionInterface::class, $this->object->getActivities());
        $this->assertInstanceOf(FlowCollectionInterface::class, $this->object->getFlows());
        $this->assertInstanceOf(GatewayCollectionInterface::class, $this->object->getGateways());
        $this->assertInstanceOf(EventCollectionInterface::class, $this->object->getEvents());
        $this->assertInstanceOf(ArtifactCollectionInterface::class, $this->object->getArtifacts());
        $this->assertInstanceOf(DataStoreCollectionInterface::class, $this->object->getDataStores());
    }
}
