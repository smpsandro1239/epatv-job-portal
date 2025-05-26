<?php

namespace Tests\Feature;

use Tests\TestCase;

test('example', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
});
