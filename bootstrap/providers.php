<?php

declare(strict_types=1);

return [
  App\Providers\AppServiceProvider::class,
  /* @chisel-registration */
  App\Providers\FortifyServiceProvider::class,
  /* @end-chisel-registration */
  App\Providers\TeamServiceProvider::class,
];
