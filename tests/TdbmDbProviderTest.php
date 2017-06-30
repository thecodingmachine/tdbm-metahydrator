<?php

namespace TheCodingMachine\TDBM\MetaHydrator;


use MetaHydrator\Exception\DBException;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\TDBM\NoBeanFoundException;
use TheCodingMachine\TDBM\TDBMService;

class TdbmDbProviderTest extends TestCase
{
    public function testGetClassName()
    {
        $tdbmService = $this->createMock(TDBMService::class);
        $tdbmService->method('getBeanClassName')
            ->willReturn('App\Bean\Foo');

        $provider = new TdbmDbProvider($tdbmService);

        $this->assertSame('App\Bean\Foo', $provider->getClassName('foo'));
    }

    public function testGetObjectNoPk()
    {
        $tdbmService = $this->createMock(TDBMService::class);
        $tdbmService->method('_getPrimaryKeysFromObjectData')
            ->willReturn([]);

        $provider = new TdbmDbProvider($tdbmService);



        $this->assertNull($provider->getObject('foo', ['some_column_not_id'=>42]));
    }

    public function testGetObjectExistingPk()
    {
        $tdbmService = $this->createMock(TDBMService::class);
        $tdbmService->method('_getPrimaryKeysFromObjectData')
            ->willReturn(['id'=>42]);

        $tdbmService->method('findObjectByPk')
            ->willReturn('some_foo');

        $provider = new TdbmDbProvider($tdbmService);



        $this->assertSame('some_foo', $provider->getObject('foo', ['id'=>42]));
    }

    public function testGetObjectNonExistingPk()
    {
        $tdbmService = $this->createMock(TDBMService::class);
        $tdbmService->method('_getPrimaryKeysFromObjectData')
            ->willReturn(['id'=>'not_exist']);

        $tdbmService->method('findObjectByPk')
            ->willThrowException(new NoBeanFoundException('No bean found'));

        $provider = new TdbmDbProvider($tdbmService);

        $this->expectException(DBException::class);
        $this->expectExceptionMessage('No bean found');

        $provider->getObject('foo', ['id'=>'not_exist']);
    }
}
