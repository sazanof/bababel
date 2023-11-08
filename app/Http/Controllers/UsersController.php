<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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

    public function getAvatar(int $id, int $size)
    {
        $user = User::find($id);
        $code = dechex(crc32("$user->firstname $user->lastname"));
        $color = '#' . substr($code, 0, 6);
        $img = Image::canvas($size, $size, $color);
        return $img->response('png');
    }

    private function utf8_char_code_at($str)
    {
        list(, $ord) = unpack('N', mb_convert_encoding($str, 'UCS-4BE', 'UTF-8'));
        return $ord;
    }

    private function intToRGB($i)
    {
        $c = ($i & 0x00FFFFFF);

        $c = dechex($c);

        return '#' . Str::padLeft($c, 6, '0');
    }
}
