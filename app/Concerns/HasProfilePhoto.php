<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Teams\Features;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
  /**
   * Update the user's profile photo.
   */
  public function updateProfilePhoto(UploadedFile $photo): void
  {
    /** @var string $id */
    $id = $this->id;
    $storagePath = $id.'/avatars';

    /** @var string|null $previous */
    $previous = $this->profile_photo_path;

    tap($previous, function ($previous) use ($photo, $storagePath): void {
      $this->forceFill([
        'profile_photo_path' => $photo->storePublicly(
          $storagePath, ['disk' => $this->profilePhotoDisk()]
        ),
      ])->save();

      if ($previous) {
        Storage::disk($this->profilePhotoDisk())->delete($previous);
      }
    });
  }

  /**
   * Delete the user's profile photo.
   */
  public function deleteProfilePhoto(): void
  {
    if (! Features::managesProfilePhotos()) {
      return;
    }

    if (is_null($this->profile_photo_path)) {
      return;
    }

    Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

    $this->forceFill([
      'profile_photo_path' => null,
    ])->save();
  }

  /**
   * Get the URL to the user's profile photo.
   *
   * @return Attribute<string, never>
   */
  protected function profilePhotoUrl(): Attribute
  {
    return Attribute::get(function (): string {
      /** @var string|null $photoPath */
      $photoPath = $this->profile_photo_path;

      if ($photoPath) {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->profilePhotoDisk());

        return $disk->url($photoPath);
      }

      return $this->defaultProfilePhotoUrl();
    });
  }

  /**
   * Get the default profile photo URL if no profile photo has been uploaded.
   */
  protected function defaultProfilePhotoUrl(): string
  {
    /** @var string $name */
    $name = $this->name;
    $name = trim(collect(explode(' ', $name))->map(fn ($segment): string => mb_substr($segment, 0, 1))->join(' '));

    return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
  }

  /**
   * Get the disk that profile photos should be stored on.
   */
  protected function profilePhotoDisk(): string
  {
    /** @var string $disk */
    $disk = Env::get('VAPOR_ARTIFACT_NAME') !== null ? 's3' : config('teams.profile_photo_disk', 'public');

    return $disk;
  }
}
