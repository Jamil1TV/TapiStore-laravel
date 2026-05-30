<?php

namespace Tests\Unit;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_basic_php_helpers_are_available(): void
    {
        require_once app_path('helpers.php');

        $this->assertTrue(function_exists('formatPrice'));
        $this->assertSame('$19.99', formatPrice(19.99));
    }
}