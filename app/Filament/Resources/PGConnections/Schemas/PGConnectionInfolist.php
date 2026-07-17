<?php

namespace App\Filament\Resources\PGConnections\Schemas;

use Filament\Schemas\Schema;

class PGConnectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
