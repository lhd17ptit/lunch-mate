<?php

namespace App\Libraries;

use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class Upload
{
    const STORAGE_PUBLIC = 'public';
    const STORAGE_S3 = 's3';

    const STORAGE_DEFAULT = self::STORAGE_PUBLIC;
    const DEFAULT_IMAGE = 'img/no-image.png';

    /**
     * Join paths
     *
     * @return string
     */
    public static function joinPaths(): string
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    /**
     * Get URL for the image
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @param string|null $defaultImage
     * @return string
     */
    public static function getUrlImage(
        $fileName,
        string $path = '',
        string $defaultImage = self::DEFAULT_IMAGE,
        string $disk = self::STORAGE_DEFAULT
    ): ?string {
        $filePath = self::joinPaths($path, $fileName);

        if (empty($fileName)) {
            return asset($defaultImage);
        }

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->url($filePath);
        }

        return asset($defaultImage);
    }

    /**
     * Get Base64 for the image
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function getBase64Image($fileName, string $path = '', string $disk = self::STORAGE_DEFAULT): ?string
    {
        $filePath = self::joinPaths($path, $fileName);
        if (empty($fileName)) {
            return null;
        }

        if (Storage::disk($disk)->exists($filePath)) {
            $data = Storage::disk($disk)->get($filePath);
            $mimyType = Storage::disk($disk)->getMimetype($filePath);;
            return 'data:' . $mimyType . ';base64,' . base64_encode($data);
        }

        return null;
    }

    /**
     * Get content for the file
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function getContentFile($fileName, string $path = '', string $disk = self::STORAGE_DEFAULT): ?string
    {
        $filePath = self::joinPaths($path, $fileName);
        if (empty($fileName)) {
            return null;
        }

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->get($filePath);
        }

        return null;
    }

    /**
     * Get mime type for the file
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function getMimetypeFile($fileName, string $path = '', string $disk = self::STORAGE_DEFAULT): ?string
    {
        $filePath = self::joinPaths($path, $fileName);
        if (empty($fileName)) {
            return null;
        }

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->getMimetype($filePath);
        }

        return null;
    }

    /**
     * Get URL for the file
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function getUrlFile($fileName, string $path = '', string $disk = self::STORAGE_DEFAULT): ?string
    {
        $filePath = self::joinPaths($path, $fileName);
        if (empty($fileName)) {
            return null;
        }

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->url($filePath);
        }

        return null;
    }

    public static function getThumbnailUrlFile($fileName, int $width = null, int $height = null, string $path = '', string $disk = self::STORAGE_DEFAULT): ?string
    {
        $filePath = self::joinPaths($path, $fileName);
        if (checkContains($fileName)) {
            return config('constants.CLOUDFRONT_URL') . '/' . $fileName;
        }

        if (empty($fileName)) {
            return null;
        }

        $imageWidth = $width ?? config('constants.default_image_width');
        $imageHeight = $height ?? config('constants.default_image_height');

        // if (Storage::disk($disk)->exists($filePath)) {
            return config('constants.CLOUDFRONT_URL') . '/' . $imageWidth . 'x' . $imageHeight . '/' . $filePath;
        // }

        return null;
    }

    /**
     * Store a file
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function storeFile(UploadedFile $file, string $path = '', string $disk = self::STORAGE_DEFAULT)
    {
        $originalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->extension();
        $filename = date('YmdHis') . '-' . uniqid() . '-' . $originalName . '.' . $extension;

        $upload = null;
        if ($disk == self::STORAGE_S3) {
            if (in_array($extension, ['heic', 'heif'])) {
                // Convert .heic/.heif to .jpg using Imagick

                // Create a temporary file to store the converted image
                $tempFilePath = sys_get_temp_dir() . '/' . uniqid('heic_to_jpg_') . '.jpg';
                $image = new Imagick();
                $image->readImageBlob($file->getContent());
                $image->setImageFormat('jpg');
                $image->setImageCompressionQuality(90);
                $image->writeImage($tempFilePath);

                $filename = date('YmdHis') . '-' . uniqid() . '-' . $originalName . '.jpg';
                $upload = Storage::disk($disk)->put($path . '/' . $filename, file_get_contents($tempFilePath));
                unlink($tempFilePath);
            } else {
                $fileContent = $file->getContent();
                $upload = Storage::disk('s3')->put($path . '/' . $filename, $fileContent);
            }
        }

        if ($disk == self::STORAGE_PUBLIC) {
            $filePath = self::joinPaths($path, $filename);
            $upload = Storage::disk($disk)->put($filePath, $file->getContent());
        }

        if ($upload) {
            return self::joinPaths($path, $filename);
        }

        return null;
    }

    public static function storeFilePDF(UploadedFile $file, string $path = '', string $disk = self::STORAGE_DEFAULT)
    {
        $originalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->extension();
        $filename = time().'_'.$originalName . '.' . $extension;

        $upload = null;

        if ($extension == 'docx' || $extension == 'doc') {
            try {
                $tempFilePath = storage_path('app/' . $filename);
                $file->move(storage_path('app'), $filename);
                $phpWord = new PhpWord();
                $phpWord = IOFactory::load($tempFilePath);
        
                $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
                $htmlFilePath = storage_path('app/converted.html');
                $htmlWriter->save($htmlFilePath);

                $dompdf = new Dompdf();
                $dompdf->loadHtml(file_get_contents($htmlFilePath));
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfContent = $dompdf->output();
        
                $disk = 's3';
                $filename = time().'_'.$originalName . '.pdf';
                $upload = Storage::disk($disk)->put($path . '/' . $filename, $pdfContent);
        
                unlink($tempFilePath);
                unlink($htmlFilePath);
            } catch (\Exception $e) {
                Log::debug(''.$e->getMessage());
            }    
        } else {
            if ($disk == self::STORAGE_S3) {
                $fileContent = $file->getContent();
                $upload = Storage::disk('s3')->put($path . '/' . $filename, $fileContent);
            }
        }

        

        if ($upload) {
            return self::joinPaths($path, $filename);
        }

        return null;
    }

    public static function storeFileFromLinkOnline($localFilePath, string $path = '', string $disk = self::STORAGE_DEFAULT)
    {
        $filename = date('YmdHis') . '.jpg';
        $upload = null;
        if ($disk == self::STORAGE_S3) {
            $upload = Storage::disk('s3')->put($path . '/' . $filename, file_get_contents($localFilePath));
        }

        if ($upload) {
            return self::joinPaths($path, $filename);
        }

        return null;
    }

    /**
     * Multiple store files
     *
     * @param $files
     * @param string $path
     * @param string $disk
     * @return array
     */
    public static function storeMultiFiles($files, string $path = '', string $disk = self::STORAGE_DEFAULT): array
    {
        $listNameFiles = [];
        foreach ($files as $file) {
            $listNameFiles[] = self::storeFile($file, $path, $disk);
        }

        return $listNameFiles;
    }

    /**
     * Delete a image
     *
     * @param $fileName
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function deleteFile($fileName, string $path = '', string $disk = self::STORAGE_DEFAULT): bool
    {
        $filePath = self::joinPaths($path, $fileName);
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }

        return false;
    }
}

