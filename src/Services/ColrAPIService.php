<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\Color;

class ColrAPIService
{

    public function __construct(
        private HttpClientInterface $colrClient, 
        private ParameterBagInterface $params, 
        private EntityManagerInterface $em, 
        )
    {
        $this->params = $params;
    }

    // Create ad
    public function createColor()
    {
        $response = $this->colrClient->request(
            'GET',
            $this->params->get('colr.url') . '/json/color/random'
        );

        $color = $response->toArray();
        $code = $color['colors'][0]['hex'];
        $name = $color['colors'][0]['tags'][0]['name'];

        $colorCreated = [
            'name' => $name,
            'code' => $code
        ];
    
        return $colorCreated;
    }

    function isColorDark($colorCreated) {
        $color = $colorCreated['code'];

        $red = hexdec(substr($color, 1, 2));
        $green = hexdec(substr($color, 3, 2));
        $blue = hexdec(substr($color, 5, 2));
        
        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);
        $luminosity = ($max + $min) / 2;
        $dark = $luminosity < 128;
        
        return $dark;
    }

    function createImage($colorCreated = null, $dark = null) {
        $color = $colorCreated['code'];

        $red = hexdec(substr($color, 1, 2));
        $green = hexdec(substr($color, 3, 2));
        $blue = hexdec(substr($color, 5, 2));

        $text = ucfirst($colorCreated['name']);
        $credit_text = "Image by " . $this->params->get('name');

        $width = 1080;
        $height = 1080;
        $image = imagecreatetruecolor($width, $height);
        $background_color = imagecolorallocate($image, $red, $green, $blue);
        imagefill($image, 0, 0, $background_color);

        if($dark) {
            $text_color = imagecolorallocate($image,  255, 255, 255);
        } else {
            $text_color = imagecolorallocate($image, 0, 0, 0);
        }

        $font_file = 'fonts/Kanit-Black.ttf';
        $font_size = 60;
        $font_angle = 0;
        $font_box = imagettfbbox($font_size, $font_angle, $font_file, $text);
        $font_width = $font_box[2] - $font_box[0];
        $font_x = ($width - $font_width) / 2;
        $font_y = ($height - $font_size) / 2 + $font_size;
        $font_color = $text_color;
        imagettftext($image, $font_size, $font_angle, $font_x, $font_y, $font_color, $font_file, $text);

        $credit_font_file = 'fonts/Roboto-Regular.ttf';
        $credit_font_size = 20;
        $credit_font_angle = 0;
        $credit_text_box = imagettfbbox($credit_font_size, $credit_font_angle, $credit_font_file, $credit_text);
        $credit_text_width = $credit_text_box[2] - $credit_text_box[0];
        $credit_text_height = $credit_text_box[1] - $credit_text_box[7];
        $credit_text_x = ($width - $credit_text_width) / 2;
        $credit_text_y = $height - $credit_text_height - 20;
        $credit_font_color = $text_color;
        imagettftext($image, $credit_font_size, $credit_font_angle, $credit_text_x, $credit_text_y, $credit_font_color, $credit_font_file, $credit_text);

        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();

        $imageCreated = 'data:image/png;base64,' . base64_encode($image_data);

        return $imageCreated;
    }

    function saveColor($colorCreated = null, $dark = null, $imageCreated = null) {
        $name = $colorCreated['code'];
        $text = $colorCreated['name'];

        $color = new Color();
        
        if($color) {
            $color->setCode($name);
            $color->setName($text);
            $color->setImage($imageCreated);
            $color->setIsDark($dark);
            $this->em->persist($color);
            $this->em->flush();
        }
    }

    function handleColor($colorCreated = null) {
        $colorCreated = $this->createColor();
        $dark = $this->isColorDark($colorCreated);
        $imageCreated = $this->createImage($colorCreated, $dark);
        $this->saveColor($colorCreated, $dark, $imageCreated);
    }
}
