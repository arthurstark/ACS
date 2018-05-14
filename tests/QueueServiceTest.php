<?php

namespace ACS\Tests;

use ACS\Aircraft;
use ACS\AircraftRequest;
use ACS\AircraftSize;
use ACS\AircraftType;
use ACS\QueueService;
use PHPUnit\Framework\TestCase;
use SplQueue;

final class QueueServiceTest extends TestCase
{

    public function testBoot()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);
        $queueService->aqm_request_process(AircraftRequest::BOOT);
        $this->assertInstanceOf(
            QueueService::class,
            $queueService
        );
    }

    public function testEnqueue()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);

        /** @var SplQueue $queue */
        $this->assertTrue($queueService->getQueue()->count() === 0);

        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::PASSENGER, AircraftSize::LARGE)
        );

        /** @var SplQueue $queue */
        $this->assertTrue($queueService->getQueue()->count() === 1);
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeue()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);

        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::PASSENGER, AircraftSize::LARGE)
        );

        $this->assertTrue($queueService->getQueue()->count() === 1);

        $queueService->aqm_request_process(AircraftRequest::DEQUEUE);

        $this->assertTrue($queueService->getQueue()->count() === 0);
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeueLargePassenger()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);

        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::LARGE)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::PASSENGER, AircraftSize::LARGE)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::SMALL)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::PASSENGER, AircraftSize::SMALL)
        );

        $this->assertTrue($queueService->getQueue()->count() === 4);

        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);

        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 3);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued->getType() === AircraftType::PASSENGER);
        $this->assertTrue($dequeued->getSize() === AircraftSize::LARGE);
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeueSmallPassenger()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);

        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::LARGE)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::SMALL)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::PASSENGER, AircraftSize::SMALL)
        );

        $this->assertTrue($queueService->getQueue()->count() === 3);

        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);

        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 2);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued->getType() === AircraftType::PASSENGER);
        $this->assertTrue($dequeued->getSize() === AircraftSize::SMALL);
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeueLargeCargo()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);


        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::SMALL)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::LARGE)
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            new Aircraft(AircraftType::CARGO, AircraftSize::SMALL)
        );

        $this->assertTrue($queueService->getQueue()->count() === 3);

        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);

        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 2);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued->getType() === AircraftType::CARGO);
        $this->assertTrue($dequeued->getSize() === AircraftSize::LARGE);
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeueSmallCargoAndEarliestArrival()
    {
        $queue = new SplQueue();
        $queueService = new QueueService($queue);

        $firstCargo = new Aircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $secondCargo = new Aircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $thirdCargo = new Aircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $fourthCargo = new Aircraft(AircraftType::CARGO, AircraftSize::SMALL);

        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            $firstCargo
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            $secondCargo
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            $thirdCargo
        );
        $queueService->aqm_request_process(AircraftRequest::ENQUEUE,
            $fourthCargo
        );


        $this->assertTrue($queueService->getQueue()->count() === 4);


        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);
        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 3);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued === $firstCargo);


        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);
        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 2);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued === $secondCargo);


        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);
        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 1);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued === $thirdCargo);

        $dequeued = $queueService->aqm_request_process(AircraftRequest::DEQUEUE);
        $queue = $queueService->getQueue();
        $this->assertTrue($queue->count() === 0);
        $this->assertInstanceOf(
            Aircraft::class,
            $dequeued
        );
        $this->assertTrue($dequeued === $fourthCargo);
    }
}
