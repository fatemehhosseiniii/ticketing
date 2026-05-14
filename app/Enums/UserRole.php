<?php

namespace App\Enums;

enum UserRole: int
{
    case User = 0;
    case LevelOne = 1;
    case LevelTwo = 2;

    public function key(): string
    {
        return match ($this) {
            self::User     => 'user',
            self::LevelOne       => 'level_one',
            self::LevelTwo       => 'level_two',
        };
    }
    public function label(): string
    {
        return match ($this) {
            self::User     => 'User',
            self::LevelOne     => 'Level one',
            self::LevelTwo     => 'Level two',
        };
    }

    public function toArray(): array
    {
        return [
            'type'       => $this->value,
            'key'        => $this->key(),
            'label'        => $this->label()
        ];
    }

}
