[![Build Status](https://travis-ci.com/zualex/memcached-client.svg?branch=master)](https://travis-ci.com/zualex/memcached-client)

# PHP memcached client

## WHY?
For fun.

## What is working?
- Get, set, and delete functions.
- Key expiration.
- [ ] Async todo.

## Usage example

    $memcached = new Client;
    $memcached->setServer('localhost', 11211);

    $memcached->set('foo', 'bar');
    $memcached->get('foo');