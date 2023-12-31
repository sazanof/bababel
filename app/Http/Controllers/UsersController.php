<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotFoundException;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageCache;
use Laravolt\Avatar\Facade as Avatar;

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

    public function getAvatar(int $id, int $size = null)
    {
        $size = is_null($size) ? 64 : $size;
        $user = User::find($id);
        if ($user instanceof User) {
            if ($user->photo !== null) {
                $path = Storage::drive('private')->path('avatars' . DIRECTORY_SEPARATOR . $user->id) . DIRECTORY_SEPARATOR . 'thumb.jpg';
                if (File::exists($path)) {
                    return Image::make($path)->response('jpg');
                }
            }

            $name = $user->lastname . ' ' . $user->firstname;
            $color = dechex(crc32($name . $id));
            $color = substr($color, 0, 6);
            return Avatar::create($name)
                ->setDimension($size)
                ->setBackground($color)
                ->setBorder(0, null)
                ->setFontSize($size * 0.5)
                ->getImageObject()
                ->response('png');
        }
        throw new NotFoundException();

    }

    public function getProfileAvatar(Request $request, int $size = null)
    {
        return $this->getAvatar($request->user()->id, $size);
    }

    private function HTMLToRGB($htmlCode)
    {
        if ($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

        if (strlen($htmlCode) == 3) {
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }

        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);

        return $b + ($g << 0x8) + ($r << 0x10);
    }

    private function RGBToHSL($RGB)
    {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float)$r) / 255.0;
        $g = ((float)$g) / 255.0;
        $b = ((float)$b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        if ($maxC == $minC) {
            $s = 0;
            $h = 0;
        } else {
            if ($l < .5) {
                $s = ($maxC - $minC) / ($maxC + $minC);
            } else {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if ($r == $maxC)
                $h = ($g - $b) / ($maxC - $minC);
            if ($g == $maxC)
                $h = 2.0 + ($b - $r) / ($maxC - $minC);
            if ($b == $maxC)
                $h = 4.0 + ($r - $g) / ($maxC - $minC);

            $h = $h / 6.0;
        }

        $h = (int)round(255.0 * $h);
        $s = (int)round(255.0 * $s);
        $l = (int)round(255.0 * $l);

        return (object)array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
    }

    /**
     * @param UploadedFile $file
     * @param array $coords
     * @param array $path
     * @return bool
     */
    public static function cropPhoto(UploadedFile $file, array $coords, array $path): bool
    {
        $w = $coords['width'];
        $h = $coords['height'];
        $x = $coords['left'];
        $y = $coords['top'];
        $img = Image::make($file);
        $imgWidth = $img->getWidth();
        $imgHeight = $img->getHeight();
        $save_path = $path['original'];
        $save_thumb_path = $path['thumb'];
        $r = Image::make($file)->resize($imgWidth, $imgHeight)->save($save_path);
        if (Image::make($r)->crop($w, $h, $x, $y)->save($save_thumb_path)) {
            return true;
        }
        return false;
    }

    public function updateAvatar(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();
        $file = $request->file('avatar');
        $coordinates = $request->get('coordinates');
        $path = '/private/avatars/' . Auth::id();
        $original = $path . DIRECTORY_SEPARATOR . 'original.jpg';
        $thumb = $path . DIRECTORY_SEPARATOR . 'thumb.jpg';
        if (!Storage::directoryExists($path)) {
            Storage::makeDirectory($path);
        }
        if (self::cropPhoto($file, $coordinates, [
            'original' => Storage::path($original),
            'thumb' => Storage::path($thumb),
        ])) {
            $user->photo = '/users/' . $user->id . '/avatar';
            $user->save();
        }
        return $user;
    }
}
