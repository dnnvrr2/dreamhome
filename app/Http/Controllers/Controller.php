<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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

    protected function nextPrefixedId(string $table, string $column, string $prefix, int $digits): string
    {
        $pattern = '^' . preg_quote($prefix, '/') . '[0-9]{' . $digits . '}$';
        $numberStart = strlen($prefix) + 1;

        $row = DB::selectOne("
            SELECT MAX(CAST(SUBSTRING({$column} FROM {$numberStart}) AS INTEGER)) AS max_number
            FROM {$table}
            WHERE {$column} ~ ?
        ", [$pattern]);

        $nextNumber = ((int) ($row->max_number ?? 0)) + 1;

        return $prefix . str_pad((string) $nextNumber, $digits, '0', STR_PAD_LEFT);
    }
}
