<?php

namespace App\Enums;

enum TicketStatus: int
{
    case New = 0;
    case Accepted = 1;
    case Approved = 2;
    case Rejected = 3;

    public function key(): string
    {
        return match ($this) {
            self::New     => 'new',
            self::Accepted       => 'accepted',
            self::Approved       => 'approved',
            self::Rejected       => 'rejected',
        };
    }
    public function label(): string
    {
        return match ($this) {
            self::New     => 'New',
            self::Accepted     => 'Accepted',
            self::Approved     => 'Approved',
            self::Rejected     => 'Rejected'
        };
    }
    public function class(): string
    {
        return match ($this) {
            self::New     => 'new',
            self::Accepted     => 'accepted',
            self::Approved     => 'approved',
            self::Rejected     => 'rejected'
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
