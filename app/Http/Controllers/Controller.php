<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;

abstract class Controller
{
    protected function databaseError(QueryException $exception, string $fallback = 'The database rejected this action.'): string
    {
        $message = $exception->errorInfo[2] ?? $exception->getMessage();

        if (preg_match('/ERROR:\s*(.+?)(?:\n|CONTEXT:|$)/s', $message, $matches)) {
            return trim($matches[1]);
        }

        return $fallback;
    }
}
