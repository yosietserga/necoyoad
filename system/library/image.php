<?php

final class Image {

    private $file;
    private $image;
    private $info;
    private $bgColor;

    public function __construct($file) {
        if (file_exists($file)) {
            $this->file = $file;

            $info = getimagesize($file);

            $this->info = array(
                'width' => $info[0],
                'height' => $info[1],
                'bits' => $info['bits'],
                'mime' => $info['mime']
            );

            $this->bgColor['r'] = IMAGE_BG_COLOR_R;
            $this->bgColor['g'] = IMAGE_BG_COLOR_G;
            $this->bgColor['b'] = IMAGE_BG_COLOR_B;

            $this->image = $this->create($file);
        } else {
            echo('Error: Could not load image ' . $file . '!');
        }
    }

    private function create($image) {
        $info = getimagesize($image);

        $mime = $info['mime'];
        
        if ($mime == 'image/gif') {
            return imagecreatefromgif($image);
        } elseif ($mime == 'image/png') {
            return imagecreatefrompng($image);
        } elseif ($mime == 'image/jpeg') {
            return imagecreatefromjpeg($image);
        }
    }

    public function setBgColor($r, $g, $b) {
        $this->bgColor['r'] = $r;
        $this->bgColor['g'] = $g;
        $this->bgColor['b'] = $b;
    }

    public function save($file, $quality = 100, $source = null) {
        $info = pathinfo($file);

        $extension = strtolower($info['extension']);

        if ($source !== null) {
            $image = $source;
        } else {
            $image = $this->image;
        }
        
        if ($extension == 'jpeg' || $extension == 'jpg') {
            imagejpeg($image, $file, $quality);
        } elseif ($extension == 'png') {
            imagepng($image, $file, 0);
        } elseif ($extension == 'gif') {
            imagegif($image, $file);
        }

        imagedestroy($image);
    }

    public function resize($width = 0, $height = 0) {
        if (!$this->info['width'] || !$this->info['height']) {
            return;
        }
        if (
            !is_numeric((int)$this->info['width'])
            || !is_numeric((int)$this->info['height'])
            || !is_numeric((int)$width)
            || !is_numeric((int)$height)
            || (int)$width === 0
            || (int)$height === 0
            || (int)$this->info['width'] === 0
            || (int)$this->info['height'] === 0
        ) {
            return;
        }

        $scale = min($width / $this->info['width'], $height / $this->info['height']);

        if ($scale == 1) {
            return;
        }

        $new_width = (int) ($this->info['width'] * $scale);
        $new_height = (int) ($this->info['height'] * $scale);
        $xpos = (int) (($width - $new_width) / 2);
        $ypos = (int) (($height - $new_height) / 2);

        $image_old = $this->image;
        $this->image = imagecreatetruecolor($width, $height);

        if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            $background = imagecolorallocatealpha($this->image, $this->bgColor['r'], $this->bgColor['g'], $this->bgColor['b'], 127);
            imagecolortransparent($this->image, $background);
        } else {
            $background = imagecolorallocate($this->image, $this->bgColor['r'], $this->bgColor['g'], $this->bgColor['b']);
        }

        imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
        
        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);

        $this->info['width'] = $width;
        $this->info['height'] = $height;
    }

    public static function resizeAndSave($filename, $width, $height, $path = null) {
        if (isset($path)) {
            $folder = $path;
        } else {
            $folder = DIR_IMAGE;
        }

        if (!file_exists($folder . $filename) || !is_file($folder . $filename)) {
            return;
        }

        $old_image = $filename;
        $extension = pathinfo($folder . $filename);
        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.'. strtolower($extension['extension']);

        if (!file_exists($folder . $new_image) || (filemtime($folder . $old_image) > filemtime($folder . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists($folder . $path)) {
                    @mkdir($folder . $path, 0777);
                }
            }

            $image = new Image($folder . $old_image);
            $image->resize($width, $height);
            $image->save($folder . $new_image);
        }

        return HTTP_IMAGE . $new_image;
    }

    public function watermark($image_path, $stamp, $position = null) {
        if (isset($path)) {
            $folder = $path;
        } else {
            $folder = DIR_IMAGE;
        }

        if (file_exists($image_path)) {
            $image = $this->create($image_path);
        } else {
            $image = $this->image;
        }

        $watermark = $this->create($folder.$stamp);
        
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);

        switch ($position) {
            default:
                $watermark_pos_x = ($this->info['width'] - $watermark_width) / 2;
                $watermark_pos_y = ($this->info['height'] - $watermark_height) / 2;
                break;
            case 'topleft':
                $watermark_pos_x = 0;
                $watermark_pos_y = 0;
                break;
            case 'topright':
                $watermark_pos_x = $this->info['width'] - $watermark_width;
                $watermark_pos_y = 0;
                break;
            case 'bottomleft':
                $watermark_pos_x = 0;
                $watermark_pos_y = $this->info['height'] - $watermark_height;
                break;
            case 'bottomright':
                $watermark_pos_x = $this->info['width'] - $watermark_width;
                $watermark_pos_y = $this->info['height'] - $watermark_height;
                break;
        }

        imagecopy($image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
        
        imagedestroy($watermark);
        $this->save($image_path, 70, $image);
    }

    public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
        $image_old = $this->image;
        $this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

        imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);

        $this->info['width'] = $bottom_x - $top_x;
        $this->info['height'] = $bottom_y - $top_y;
    }

    public function rotate($degree, $color = 'FFFFFF') {
        $rgb = $this->html2rgb($color);

        $this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

        $this->info['width'] = imagesx($this->image);
        $this->info['height'] = imagesy($this->image);
    }

    private function filter($filter) {
        imagefilter($this->image, $filter);
    }

    private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
        $rgb = $this->html2rgb($color);

        imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
    }

    private function merge($file, $x = 0, $y = 0, $opacity = 100) {
        $merge = $this->create($file);

        $merge_width = imagesx($this->image);
        $merge_height = imagesy($this->image);

        imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
    }

    private function html2rgb($color) {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }

}

class NTImage {
    
    public static $watermark = null;
    public static $position = null;
    
    public static function setWatermark($file, $position = null) {
        self::$watermark = $file;
        self::$position = $position;
    }

    public static function resizeAndSave($filename, $width, $height, $path = null) {
        if (isset($path)) {
            $folder = $path;
        } else {
            $folder = DIR_IMAGE;
        }

        if (!file_exists($folder . $filename) || !is_file($folder . $filename)) {
            $filename = "no_image.jpg";
        }

        $old_image = $filename;
        $extension = pathinfo($folder . $filename);
        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height .'.'. $extension['extension'];

        if (!file_exists($folder . $new_image) || (filemtime($folder . $old_image) > filemtime($folder . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));
            
            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists($folder . $path)) {
                    @mkdir($folder . $path, 0777);
                }
            }
            
            $image = new Image($folder . $old_image);
            $image->resize($width, $height);
            $image->save($folder . $new_image);

            if (self::$watermark) {
                $position = (self::$position) ? self::$position : null;
                $image->watermark($folder . $new_image, self::$watermark, $position);
            }
        }

        return HTTP_IMAGE . $new_image;
    }

}
