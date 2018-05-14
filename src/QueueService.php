<?php

namespace ACS;


use SplQueue;

/**
 * Air-Traffic Control System Queue Manager
 *
 * Class QueueService
 * @package ACS
 */
class QueueService
{

    /**
     * Queue object from PHP built in data structures
     *
     * @var SplQueue
     */
    private $queue;


    /**
     * QueueService constructor.
     *
     * @param SplQueue $queue
     */
    public function __construct(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Main aircraft request function
     *
     * @param $request
     * @param null $aircraft
     * @return Aircraft|null
     */
    public function aqm_request_process($request, $aircraft = null)
    {
        switch ($request) {
            case AircraftRequest::BOOT:
                $this->boot();
                break;
            case AircraftRequest::ENQUEUE:
                try {
                    $this->enqueue($aircraft);
                } catch (\Exception $e) {
                    // Log with logger / file output file
                }
                break;
            case AircraftRequest::DEQUEUE:
                return $this->dequeue();
                break;
        }
    }

    /**
     * "System boot used to start the system."
     */
    private function boot()
    {
    }

    /**
     * Add an aircraft to the queue
     *
     * @param $type
     * @param $size
     * @throws \Exception
     */
    private function enqueue(Aircraft $aircraft)
    {
        $this->queue->push($aircraft);
    }

    /**
     * Removes Aircraft from the queue
     */
    private function dequeue()
    {
        $dequeueCandidate = null;

        if ($this->queue->count() !== 0) {

            /** @var Aircraft $dequeueCandidate */
            $dequeueCandidate = $this->queue->offsetGet(0);
            $candidateIndex = 0;

            $i = 0;

            /** @var Aircraft $aircraft */
            foreach ($this->queue as $aircraft) {

                if ($aircraft->getType() === AircraftType::PASSENGER) {

                    // First passenger small plane
                    if ($dequeueCandidate->getType() !== AircraftType::PASSENGER) {
                        $dequeueCandidate = $aircraft;
                        $candidateIndex = $i;
                    }

                    // Found first large passenger
                    if ($aircraft->getSize() === AircraftSize::LARGE) {
                        $dequeueCandidate = $aircraft;
                        $candidateIndex = $i;
                        break;
                    }

                } else {

                    if (
                        $dequeueCandidate->getType() === AircraftType::CARGO &&
                        $dequeueCandidate->getSize() === AircraftSize::SMALL
                    ) {

                        // Found large carge
                        if ($aircraft->getSize() === AircraftSize::LARGE) {
                            $dequeueCandidate = $aircraft;
                            $candidateIndex = $i;
                        }
                    }
                }

                $i++;
            }

            $this->queue->offsetUnset($candidateIndex);
        }

        return $dequeueCandidate;
    }

    /**
     * Used for unit testing / inspecting items in queue
     *
     * @return SplQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

}
