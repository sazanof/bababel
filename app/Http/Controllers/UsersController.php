<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function searchUsers(Request $request)
    {
        $term = $request->get('term');
        return User
            ::where(function (Builder $builder) use ($term) {
                return $builder
                    ->orWhere('username', 'LIKE', $term . '%')
                    ->orWhere('firstname', 'LIKE', $term . '%')
                    ->orWhere('lastname', 'LIKE', $term . '%')
                    ->orWhere('email', 'LIKE', $term . '%');
            })
            ->select([
                'id',
                'username',
                'firstname',
                'lastname',
                'photo',
                'email',
                'position',
                'department'
            ])
            ->orderBy('lastname', 'asc')
            ->limit(50)
            ->get();
    }
}
