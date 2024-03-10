<?php

namespace Smoren\ArrayView\Structs;

use Smoren\ArrayView\Exceptions\IndexError;
use Smoren\ArrayView\Exceptions\ValueError;
use Smoren\ArrayView\Util;

/**
 * @property-read int|null $start
 * @property-read int|null $end
 * @property-read int|null $step
 */
class Slice
{
    /**
     * @var int|null
     */
    public ?int $start;
    /**
     * @var int|null
     */
    public ?int $end;
    /**
     * @var int|null
     */
    public ?int $step;

    /**
     * @param string|Slice $s
     * @return void
     */
    public static function toSlice($s): Slice
    {
        if ($s instanceof Slice) {
            return $s;
        }

        if (!static::isSliceString($s)) {
            throw new ValueError("Invalid slice: \"{$s}\".");
        }

        $slice = static::parseSliceString($s);

        return new Slice(...$slice);
    }

    /**
     * @param mixed $s
     * @return bool
     */
    public static function isSlice($s): bool
    {
        return ($s instanceof Slice) || static::isSliceString($s);
    }

    /**
     * @param mixed $s
     * @return bool
     */
    public static function isSliceString($s): bool
    {
        if (!\is_string($s)) {
            return false;
        }

        if (\is_numeric($s)) {
            return false;
        }

        if (!\preg_match('/^-?[0-9]*:?-?[0-9]*:?-?[0-9]*$/', $s)) {
            return false;
        }

        $slice = static::parseSliceString($s);

        return !(\count($slice) < 1 || \count($slice) > 3);
    }

    /**
     * @param int|null $start
     * @param int|null $end
     * @param int|null $step
     */
    public function __construct(?int $start = null, ?int $end = null, ?int $step = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
    }

    public function normalize(int $containerLength): NormalizedSlice
    {
        // TODO: Need refactor
        $step = $this->step ?? 1;

        if ($step === 0) {
            throw new IndexError("Step cannot be 0.");
        }

        $defaultEnd = ($step < 0 && $this->end === null) ? -1 : null;

        $start = $this->start ?? ($step > 0 ? 0 : $containerLength - 1);
        $end = $this->end ?? ($step > 0 ? $containerLength : -1);

        $start = round($start);
        $end = round($end);
        $step = round($step);

        $start = Util::normalizeIndex($start, $containerLength, false);
        $end = Util::normalizeIndex($end, $containerLength, false);

        if ($step > 0 && $start >= $containerLength) {
            $start = $end = $containerLength - 1;
        } elseif ($step < 0 && $start < 0) {
            $start = $end = 0;
            $defaultEnd = 0;
        }

        $start = $this->squeezeInBounds($start, 0, $containerLength - 1);
        $end = $this->squeezeInBounds($end, $step > 0 ? 0 : -1, $containerLength);

        if (($step > 0 && $end < $start) || ($step < 0 && $end > $start)) {
            $end = $start;
        }

        return new NormalizedSlice($start, $defaultEnd ?? $end, $step);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        [$start, $end, $step] = [$this->start ?? '', $this->end ?? '', $this->step ?? ''];
        return "{$start}:{$end}:{$step}";
    }

    /**
     * @param string $s
     * @return array<int>
     */
    private static function parseSliceString(string $s): array
    {
        return array_map(fn($x) => trim($x) === '' ? null : \intval(trim($x)), \explode(':', $s));
    }

    /**
     * @param int $x
     * @param int $min
     * @param int $max
     * @return int
     */
    private function squeezeInBounds(int $x, int $min, int $max): int
    {
        return max($min, min($max, $x));
    }
}
