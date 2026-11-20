<?php
namespace app;

class Upload{
    const DIR = __DIR__ . "/../userDashboard/Images/";

    public static function save(array $file): ?string{
        if($file["error"]===UPLOAD_ERR_NO_FILE) return null;
        if($file["error"]!==UPLOAD_ERR_OK) throw new \Exception("Upload failed.");
        if($file["size"]>2*1024*1024) throw new \Exception("Max file size is 2MB.");

        $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
        $allowed=["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp"];
        if(!isset($allowed[$mime])) throw new \Exception("Only JPG, PNG or WEBP allowed.");

        $name=bin2hex(random_bytes(8)).".".$allowed[$mime];
        move_uploaded_file($file["tmp_name"], self::DIR.$name);
        return $name;
    }

    public static function delete(?string $name): void{
        if(!$name) return;
        $path=self::DIR.basename($name);
        if(is_file($path)) unlink($path);
    }
}