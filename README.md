[![Build Status](https://travis-ci.com/zualex/memcached-client.svg?branch=master)](https://travis-ci.com/zualex/memcached-client)

# Drimsim

## Тестовое задание

Необходимо реализовать библиотеку-клиент к Memcached. Библиотека должна на низком уровне реализовывать команды get/set/delete и уметь работать в синхронном и асинхронном режиме. При реализации необходимо использовать подход Test Driven Development.

Чтобы понять, как работает клиент, можно сделать вот такую telnet сессию, - она проиллюстрирует типичное общение клиента с сервером:

    $ telnet localhost 11211

    get key 
    END 
    set key 0 3600 3 
    xyz 
    STORED 
    get key 
    VALUE key 0 3 
    xyz 
    END
    
Для автоматизации проверок нужно прикрутить Travis CI и проверку кода на соотвествие стандартам PSR-2.

Также тебе пригодится документация: https://github.com/memcached/memcached/blob/master/doc/protocol.txt 


## Usage example

    $memcached = new Client;
    $memcached->setServer('localhost', 11211);

    $memcached->set('foo', 'bar');
    $memcached->get('foo');
