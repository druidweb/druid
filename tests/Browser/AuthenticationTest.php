<?php

declare(strict_types=1);

use Laravel\Dusk\Browser;

test('can visit login page', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertPathIs('/login');
  });
});

test('can visit register page', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->assertPathIs('/register');
  });
});

test('can visit home page', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->assertPathIs('/');
  });
});
