<?php

declare(strict_types=1);

namespace App\Teams;

use JsonSerializable;

class Role implements JsonSerializable
{
  /**
   * The role's description.
   */
  public string $description;

  /**
   * Create a new role instance.
   */
  public function __construct(
    /**
     * The key identifier for the role.
     */
    public string $key,
    /**
     * The name of the role.
     */
    public string $name,
    /**
     * The role's permissions.
     *
     * @var array<int, string>
     */
    public array $permissions
  ) {}

  /**
   * Describe the role.
   *
   * @return $this
   */
  public function description(string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get the JSON serializable representation of the object.
   *
   * @return array<string, mixed>
   */
  public function jsonSerialize(): array
  {
    return [
      'key' => $this->key,
      'name' => __($this->name),
      'description' => __($this->description),
      'permissions' => $this->permissions,
    ];
  }
}
