<?php

use Laravel\Dusk\Browser;

test('welcome page loads correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->pause(2000) // Wait for Vue app to mount
      ->assertSee("Let's get started")
      ->assertTitleContains('Welcome');
  });
});

test('welcome page has proper navigation', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->assertSeeLink('Log in')
      ->assertSeeLink('Register');
  });
});
