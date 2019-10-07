<?php

declare(strict_types=1);

namespace Tanuki\Delivery\Tests\Unit\Infrastructure\Storage\Postgres\Query;

use PHPUnit\Framework\TestCase;
use Sql\Query\Selecting;
use Sql\Tests\Connection\TestConnection;

class SelectingTest extends TestCase
{
    public function testSelectSuccessfullyEmptyData()
    {
        $result =
            (new Selecting(
                'select * from (values (1)) where column1 is null',
                []
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                );

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->value()->isPresent());
    }

    public function testSelectSuccessfullyWithASingleValueInsideInClause()
    {
        $result =
            (new Selecting(
                'select 1 + ? as sum',
                [4]
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                );

        $this->assertTrue($result->isSuccessful());
        $this->assertTrue($result->value()->isPresent());
        $this->assertEquals([['sum' => 5]], $result->value()->value());
    }

    /**
     * @dataProvider parametersInArrays
     */
    public function testSelectSuccessfullyWithSeveralValuesInsideInClause($query, $parameters)
    {
        $result =
            (new Selecting(
                $query,
                $parameters
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                )
        ;

        $this->assertTrue($result->isSuccessful());
        $this->assertTrue($result->value()->isPresent());
        $this->assertEquals(
            [['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]],
            $result->value()->value()
        );
    }

    public function parametersInArrays()
    {
        return
            [
                [
                    'with test_table(id) as (values ("1"),("2"),("3"),("4"),("5")) select * from test_table where id = ? or id in (?) or id = ?',
                    [
                        1,
                        [2, 3],
                        4,
                    ],
                ],
                [
                    'with test_table(id) as (values ("1"),("2"),("3"),("4"),("5")) select * from test_table where id = ? or id in (?) or id in (?)',
                    [
                        1,
                        [2, 3],
                        [4],
                    ],
                ],
                [
                    'with test_table(id) as (values ("1"),("2"),("3"),("4"),("5")) select * from test_table where id = ? or id = ? or id in (?)',
                    [
                        1,
                        4,
                        [2, 3],
                    ],
                ],
                [
                    'with test_table(id) as (values ("1"),("2"),("3"),("4"),("5")) select * from test_table where id in (?) or id in (?) or id in (?)',
                    [
                        [1],
                        [2, 3],
                        [4],
                    ],
                ],
                [
                    'with test_table(id) as (values ("1"),("2"),("3"),("4"),("5")) select * from test_table where id in (?) or id = ? or id in (?)',
                    [
                        0 => [1, 2],
                        1 => 3,
                        3 => [4],
                    ],
                ]
            ];
    }

    public function testSelectSuccessfullyNonEmptyData()
    {
        $result =
            (new Selecting(
                'select 1 + 2 as sum',
                []
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                );

        $this->assertTrue($result->isSuccessful());
        $this->assertNotEmpty($result->value());
        $this->assertEquals([['sum' => 3]], $result->value()->value());
    }

    /**
     * @dataProvider invalidQuery
     */
    public function testInsertWithInvalidQuery(string $query)
    {
        $result =
            (new Selecting(
                $query,
                array(
                    1
                )
            ))
                ->result(
                    (new TestConnection())
                        ->open()
                );

        $this->assertFalse($result->isSuccessful());
        $this->assertNotEmpty($result->error());
    }

    public function invalidQuery()
    {
        return [
            ['ssssssssssselect from _order where id = ?'],
            ['select from "orrrrrrrrrrrrrrrder" where id = ?'],
            ['select from _order where idddddddddddd = ?'],
            ['select from _order where idddddddddddd = (?'],
        ];
    }
}
