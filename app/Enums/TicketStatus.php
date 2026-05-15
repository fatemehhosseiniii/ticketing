<?php

namespace App\Enums;

enum TicketStatus: int
{
    case New = 0;
    case Accepted = 1;
    case Send = 2;
    case Rejected = 3;
    case Completed=4;

    public function key(): string
    {
        return match ($this) {
            self::New     => 'new',
            self::Accepted       => 'accepted',
            self::Send       => 'send',
            self::Rejected       => 'rejected',
            self::Completed       => 'completed',
        };
    }
    public function label(): string
    {
        return match ($this) {
            self::New     => 'New',
            self::Accepted     => 'Accepted',
            self::Send     => 'Send',
            self::Rejected     => 'Rejected',
            self::Completed     => 'Completed'
        };
    }
    public function class(): string
    {
        return match ($this) {
            self::New     => 'new',
            self::Accepted     => 'accepted',
            self::Send     => 'send',
            self::Rejected     => 'rejected',
            self::Completed     => 'completed'
        };
    }


    public function toArray(): array
    {
        return [
            'type'       => $this->value,
            'key'        => $this->key(),
            'label'        => $this->label(),
            'class'        => $this->class(),
        ];
    }

}
