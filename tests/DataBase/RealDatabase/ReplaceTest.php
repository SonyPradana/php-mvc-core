<?php

declare(strict_types=1);

namespace System\Test\Database\RealDatabase;

use System\Database\MyQuery;

final class ReplaceTest extends \RealDatabaseConnectionTest
{
    /**
     * @test
     *
     * @group database
     */
    public function itCanReplaceOnNewData()
    {
        MyQuery::from('users', $this->pdo)
            ->replace()
            ->values([
                'user' => 'sony',
                'pwd'  => 'secret',
                'stat' => 99,
            ])
            ->execute();

        $this->assertUserExist('sony');
    }

    /**
     * @test
     *
     * @group database
     */
    public function itCanReplaceOnExistData()
    {
        MyQuery::from('users', $this->pdo)
            ->insert()
            ->values([
                'user' => 'sony',
                'pwd'  => 'secret',
                'stat' => 99,
            ])
            ->execute();

        MyQuery::from('users', $this->pdo)
            ->replace()
            ->values([
                'user' => 'sony',
                'pwd'  => 'secret',
                'stat' => 66,
            ])
            ->execute();

        $this->assertUserStat('sony', 66);
    }

    /**
     * @test
     *
     * @group database
     */
    public function itCanUpdateInsertusingOneQuery()
    {
        MyQuery::from('users', $this->pdo)
            ->insert()
            ->values([
                'user' => 'sony',
                'pwd'  => 'secret',
                'stat' => 99,
            ])
            ->execute();

        MyQuery::from('users', $this->pdo)
            ->replace()
            ->rows([
                [
                    'user' => 'sony',
                    'pwd'  => 'secret',
                    'stat' => 66,
                ],
                [
                    'user' => 'sony2',
                    'pwd'  => 'secret',
                    'stat' => 66,
                ],
            ])
            ->execute();

        $this->assertUserStat('sony', 66);
        $this->assertUserExist('sony2');
    }
}
