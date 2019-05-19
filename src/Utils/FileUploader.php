<?php

namespace App\Utils;

use App\Entity\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function exists(File $file)
    {
        return file_exists(
            $this->getTargetExtensionDirectory($file->getExtension()) . '/' . $file->getUpload()
        );
    }

    public function upload(UploadedFile $file, string $fileName = null)
    {

        //$fileName = Urlizer::urlize($file->getClientOriginalName()).'-'.uniqid().'.'.$file->guessExtension();

        if (!$fileName) {
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        }

        try {
            $file->move($this->getTargetExtensionDirectory($file->getClientOriginalExtension()), $fileName);
        } catch (FileException $e) {
            var_dump($e->getMessage());
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getTargetExtensionDirectory(string $extension)
    {
        return $this->getTargetDirectory() . '/' . $extension;
    }
}