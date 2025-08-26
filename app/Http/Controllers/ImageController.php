<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Storage;
use Image;

class ImageController extends Controller {
    public function UserDNI($user_id) {
        $user = User::getItem(null, $user_id);

        $font = public_path() . '/fonts/arial.ttf';
        $im = @imagecreatefromjpeg(public_path() . '/images/credential.jpg');
        $img_with = 866;

        //Load logo
        $logo = imagecreatefrompng(public_path() . '/images/SVR.png');

        //Resize Logo
        $original_width_logo = imagesx($logo);
        $original_height_logo = imagesy($logo);
        $new_logo = imagecreatetruecolor(436, 204);
        imagealphablending($new_logo, false); 
        imagesavealpha($new_logo, true);
        $transparent = imagecolorallocatealpha($new_logo, 255, 255, 255, 127);
        imagefill($new_logo, 0, 0, $transparent);
        imagecopyresampled($new_logo, $logo, 0, 0, 0, 0, 436, 204, $original_width_logo, $original_height_logo);
        imagecopy($im, $new_logo, 216, 0, 0, 0, 436, 204);

        //Load Avatar
        if($user->avatar){
            $avatar = imagecreatefrompng(Storage::disk('User')->path($user->avatar));
        }else{
            $avatar = imagecreatefrompng(Storage::disk('User')->path('default.png'));
        }
        
        //Resize Avatar
        $original_width_avatar = imagesx($avatar);
        $original_height_avatar = imagesy($avatar);
        $new_avatar = imagecreatetruecolor(390, 390);
        imagealphablending($new_avatar, false); 
        imagesavealpha($new_avatar, true);
        $transparent = imagecolorallocatealpha($new_avatar, 255, 255, 255, 127);
        imagefill($new_avatar, 0, 0, $transparent);
        imagecopyresampled($new_avatar, $avatar, 0, 0, 0, 0, 390, 390, $original_width_avatar, $original_height_avatar);
        imagecopy($im, $new_avatar, 238, 224, 0, 0, 390, 390);

        $text_color = imagecolorallocate($im, 100, 100, 100);

        //Nombre
        $tb = imagettfbbox(50, 0, $font, GenController::getFullName($user));
        $x = ceil(($img_with - $tb[2]) / 2);
        imagettftexT($im, 50, 0, $x, 700, $text_color, $font, utf8_decode(GenController::getFullName($user)));

        //Puesto
        // $tb = imagettfbbox(35, 0, $font, $user->employment_position);
        // $x = ceil(($img_with - $tb[2]) / 2);
        // imagettftexT($im, 35, 0, $x, 800, $text_color, $font, utf8_decode($user->employment_position));

        //UUID
        $tb = imagettfbbox(25, 0, $font, $user->uiid);
        $x = ceil(($img_with - $tb[2]) / 2);
        imagettftexT($im, 25, 0, $x, 840, $text_color, $font, utf8_decode($user->uiid));

        //Vigencia
        $tb = imagettfbbox(25, 0, $font, '2030-01-01');
        $x = ceil(($img_with - $tb[2]) / 2);
        imagettftexT($im, 25, 0, $x, 880, $text_color, $font, utf8_decode('2030-01-01'));

        //Generar QR
        $qr_name = 'user_qr_' . $user_id .'.png';
         \QrCode::format('png')
            ->size(221)
            ->generate(
                'id=' . $user->id . ';name=' . $user->name . ';surname_p=' . $user->surname_p . ';surname_m=' . $user->surname_m,
                Storage::disk('temp')->path($qr_name)
            );
        
        //QR en Credencial
        $qr = imagecreatefrompng(Storage::disk('temp')->path($qr_name));
        imagecopy($im, $qr, 323, 920, 0, 0, 221, 221);

        $file_name = 'user_cedential_' . $user_id . '.jpg';
        $save = Storage::disk('temp')->path($file_name);
        imagejpeg($im, $save);
        imagedestroy($im);


        $img = file_get_contents($save);
        $jpg64 = base64_encode($img);

        // return response()->file($save);

        Storage::disk('temp')->delete($file_name);
        Storage::disk('temp')->delete($qr_name);

        return ["jpg64" => $jpg64, "jpg" => $save, "file_name" => $file_name];
    }
}
