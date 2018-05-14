This project was built to showcase my OOP skills.

## Requires:

- PHP
- composer


## Process & Assumptions:

Creation order: Aircraft enums, Aircraft object, QueueService, unit tests.

Dequeue:  

The first large passenger is dequeued.  Best case we find it quickly, worst case it is at the end of the queue.  

Most cases will require searching the entire queue unless a large passenger is found sooner.


## To run locally:
```
cd acs
composer install
./vendor/bin/phpunit tests
```
