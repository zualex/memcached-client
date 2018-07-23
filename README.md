# WHY?
For fun.

## What is working?
- Get, set, and delete functions.
- Key expiration.

## Usage example

::

    $m = new Client;
    $m->setServer('localhost', 11211);

    $m->set('foo', 'bar');
    $m->get('foo');