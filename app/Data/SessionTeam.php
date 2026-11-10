<?php

namespace App\Data;

use App\Models\Team;
use ArrayAccess;

class SessionTeam implements ArrayAccess
{
    public int $id;
    public string $name;
    public bool $personal_team;
    public bool $show_boarding;

    public function __construct(int $id, string $name = '', bool $personal_team = false, bool $show_boarding = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->personal_team = $personal_team;
        $this->show_boarding = $show_boarding;
    }

    public static function fromTeam(mixed $team): self
    {
        if ($team instanceof self) {
            return $team;
        }

        return new self(
            (int) data_get($team, 'id'),
            (string) (data_get($team, 'name') ?? ''),
            (bool) (data_get($team, 'personal_team') ?? false),
            (bool) (data_get($team, 'show_boarding') ?? false)
        );
    }

    public function is($other): bool
    {
        return $other && (int) data_get($other, 'id') === (int) $this->id;
    }

    public function getKey(): int
    {
        return $this->id;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset} ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }

    public function __get(string $name): mixed
    {
        if ($name === 'members') {
            return Team::find($this->id)?->members ?? collect();
        }

        return Team::find($this->id)?->{$name};
    }

    public function __call(string $name, array $arguments): mixed
    {
        $team = Team::find($this->id);
        if ($team && method_exists($team, $name)) {
            return $team->{$name}(...$arguments);
        }

        return null;
    }
}
