<?php

namespace App\Transformers;

use App\Models\BankEntry;
use App\Utils\Traits\MakesHash;

class BankEntryTransformer extends EntityTransformer
{
    use MakesHash;

    public function transform(BankEntry $entry)
    {
        return [
            'id' => (string) $this->encodePrimaryKey($entry->id),
            'user_id' => (string) $this->encodePrimaryKey($entry->user_id),
            'company_id' => (string) $this->encodePrimaryKey($entry->company_id),
            'project_id' => $entry->project_id ? (string) $this->encodePrimaryKey($entry->project_id) : '',
            'date' => $entry->date ? $entry->date->format('Y-m-d') : '',
            'type' => (string) $entry->type,
            'description' => (string) ($entry->description ?: ''),
            'amount' => (float) $entry->amount,
            'iva_amount' => (float) $entry->iva_amount,
            'category' => (string) ($entry->category ?: ''),
            'reference' => (string) ($entry->reference ?: ''),
            'is_deleted' => (bool) $entry->is_deleted,
            'created_at' => (int) $entry->created_at,
            'updated_at' => (int) $entry->updated_at,
            'archived_at' => (int) $entry->deleted_at,
        ];
    }
}
