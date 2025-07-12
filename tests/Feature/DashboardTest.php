<?php

test('guests are redirected to the login page', function (): void {
  $response = $this->get('/dashboard');
  $response->assertRedirect('/login');
});
