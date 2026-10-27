<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case InReview = 'in_review';
    case InContact = 'in_contact';
    case ProposalSent = 'proposal_sent';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Novo',
            self::InReview => 'Em análise',
            self::InContact => 'Em contacto',
            self::ProposalSent => 'Proposta enviada',
            self::Completed => 'Concluído',
            self::Archived => 'Arquivado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'orange',
            self::InReview => 'amber',
            self::InContact => 'blue',
            self::ProposalSent => 'purple',
            self::Completed => 'green',
            self::Archived => 'gray',
        };
    }

    /**
     * Statuses that still require action from the team.
     *
     * @return list<self>
     */
    public static function open(): array
    {
        return [self::New, self::InReview, self::InContact];
    }
}
