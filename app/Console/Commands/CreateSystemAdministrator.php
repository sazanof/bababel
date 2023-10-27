<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CreateSystemAdministrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bababel:create-system-administrator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user with all privileges';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask('Enter admin username');
        if (empty($username)) {
            return CommandAlias::FAILURE;
        }

        do {
            $password = $this->ask('Enter password');
        } while (empty($password) || Str::length($password) < 8);

        do {
            $passwordConfirmation = $this->ask('Enter password confirmation');
        } while (empty($password) || $passwordConfirmation !== $password);

        do {
            $email = $this->ask('Enter correct email address');
        } while (!filter_var($email, FILTER_VALIDATE_EMAIL));

        $firstname = $this->ask('Enter firstname');
        $lastname = $this->ask('Enter lastname');

        $this->table([
            'Username',
            'Email',
            'Firstname',
            'Lastname'
        ], [[
            $username,
            $email,
            $firstname,
            $lastname
        ]]);
        if ($this->confirm('Information is correct?')) {
            User::insertOrIgnore([
                'username' => $username,
                'password' => Hash::make($password),
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'is_admin' => true
            ]);

            $this->info('User created!');
        }

        return CommandAlias::SUCCESS;
    }
}
