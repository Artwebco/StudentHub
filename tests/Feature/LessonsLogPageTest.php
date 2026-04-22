<?php

describe('Lessons Log page', function () {
    it('loads successfully', function () {
        $response = $this->get('/lessons-log');
        $response->assertStatus(200);
        $response->assertSee('Lessons'); // Adjust text to something visible on the page
    });
});
