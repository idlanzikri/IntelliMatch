<?php

namespace Tests\Unit;

use App\Services\LocationDistance;
use PHPUnit\Framework\TestCase;

class LocationDistanceTest extends TestCase
{
    public function test_same_point_and_known_great_circle_distances(): void
    {
        $this->assertSame(0.0, LocationDistance::kilometres(3, 101, 3, 101));
        $this->assertEqualsWithDelta(10007.6, LocationDistance::kilometres(0, 0, 0, 90), 0.1);
        $this->assertEqualsWithDelta(222.4, LocationDistance::kilometres(0, 179, 0, -179), 0.1);
    }
}
