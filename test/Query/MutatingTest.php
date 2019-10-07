<?php

declare(strict_types=1);

namespace Sql\Tests\Query;

use PHPUnit\Framework\TestCase;
use Sql\Connection;
use Sql\Query\Mutating;
use Sql\Query\Selecting;
use Sql\Tests\Connection\TestConnection;

class MutatingTest extends TestCase
{
    public function testSuccessfulInsert()
    {
        $connection = new TestConnection();

        (new Mutating('create temporary table test_table (id int)', []))
            ->result($connection->open());

        $result =
            (new Mutating(
                'insert into test_table values (?), (?)',
                [2, 3]
            ))
                ->result($connection->open());

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->value()->isPresent());
        $this->assertDatabase($connection);
    }

    /**
     * @dataProvider invalidQuery
     */
    public function testInsertWithInvalidQuery(string $query)
    {
        $result =
            (new Mutating(
                $query,
                [1488, 666]
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                )
        ;

        $this->assertFalse($result->isSuccessful());
        $this->assertNotEmpty($result->error());
    }

    public function invalidQuery()
    {
        return [
            ['inssssssert into _order values (?, ?)'],
            ['insert into orrrrrrder values (?, ?)'],
            ['insert into _order values (?, ?, ?)'],
            ['insert into _order values (?)'],
            ['insert into _order values (?'],
        ];
    }

    private function assertDatabase(Connection $connection)
    {
        $result =
            (new Selecting('select * from test_table', []))
                ->result($connection->open());

        $this->assertTrue($result->isSuccessful());
        $this->assertTrue($result->value()->isPresent());
        $this->assertEquals(
            [['id' => 2], ['id' => 3]],
            $result->value()->value()
        );
    }
}
