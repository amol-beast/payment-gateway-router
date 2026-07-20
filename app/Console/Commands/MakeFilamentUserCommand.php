<?php

namespace App\Console\Commands;

use Filament\Commands\MakeUserCommand;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:filament-user', aliases: [
    'filament:make-user',
    'filament:user',
])]
class MakeFilamentUserCommand extends MakeUserCommand
{
    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            ...parent::getOptions(),
            new InputOption(
                name: 'role',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The role to assign to the user (e.g. superadmin, admin, user)',
            ),
        ];
    }

    protected function createUser(): Model&Authenticatable
    {
        $role = $this->option('role');

        if ($role !== null && ! Role::where('name', $role)->exists()) {
            $this->fail("Role \"{$role}\" does not exist. Available roles: ".Role::pluck('name')->implode(', '));
        }

        $user = parent::createUser();

        if ($role !== null && method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }

        return $user;
    }

    protected function sendSuccessMessage(Model&Authenticatable $user): void
    {
        parent::sendSuccessMessage($user);

        if ($role = $this->option('role')) {
            $this->components->info("Assigned role \"{$role}\" to ".($user->getAttribute('email') ?? 'the user').'.');
        }
    }
}
