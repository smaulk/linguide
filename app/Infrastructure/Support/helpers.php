<?php

if (!function_exists('chunk_iterable')) {
    /**
     * @template T
     * @param iterable<T> $items
     * @return iterable<T[]>
     */
    function chunk_iterable(iterable $items, int $size): iterable
    {
        $buffer = [];

        foreach ($items as $item) {
            $buffer[] = $item;

            if (count($buffer) >= $size) {
                yield $buffer;
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            yield $buffer;
        }
    }
}