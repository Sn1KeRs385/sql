<?php

declare(strict_types=1);

namespace Sql\Query;

use Impiura\Error\FromString;
use Impiura\Result;
use Impiura\Result\Declined;
use Impiura\Result\Successful;
use Impiura\Value\Emptie;
use Impiura\Value\Present;
use PDO;
use Sql\Query;
use Throwable;
use Exception;

class Selecting implements Query
{
    private $queryString;
    private $values;

    public function __construct(string $queryString, array $values)
    {
        $this->queryString = $queryString;
        $this->values = array_values($values);
    }

    public function result(PDO $pdo): Result
    {
        try {
            $statement = $pdo->prepare($this->fillQuestionMarksInsideInClause($this->queryString));
        } catch (Throwable $e) {
            return new Declined(new FromString($e->getMessage()));
        }

        try {
            $result = $statement->execute($this->values);
        } catch (Throwable $e) {
            return new Declined(new FromString($e->getMessage()));
        }

        if ($result === false) {
            return new Declined(new FromString($statement->errorInfo()[2]));
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $statement->closeCursor();
        $statement = null;

        if (count($result) == 0) {
            return new Successful(new Emptie());
        }

        return new Successful(new Present($result));
    }

    private function fillQuestionMarksInsideInClause(string $query)
    {
        // we need to walk and replace ? in query string backward
        $reversedValues = array_reverse($this->values, true);
        $explodedValues = [];
        foreach ($reversedValues as $key => $value) {
            if (is_array($value)) {
                if (empty($value)) {
                    throw new Exception('Array in IN clause can not be empty');
                }
                $positionInQuery = $this->findNthPosition($query, '?', $key + 1);
                $explodedQuestions = '?'. str_repeat(', ?', count($value) - 1);
                $query = substr_replace($query, $explodedQuestions, $positionInQuery, 1);
                array_push($explodedValues, ...array_reverse($value));
            } else {
                $explodedValues[] = $value;
            }
        }
        $this->values = array_reverse($explodedValues);
        return $query;
    }

    private function findNthPosition(string $what, string $where, int $nth)
    {
        $arr = explode($where, $what);
        switch( $nth )
        {
            case $nth == 0:
                return false;
                break;

            case $nth > max(array_keys($arr)):
                return false;
                break;

            default:
                return strlen(implode($where, array_slice($arr, 0, $nth)));
        }
    }
}
