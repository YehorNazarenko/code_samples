<?php
namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\File;

class FileHelper {
    public static function uploadFileToAws($data, $path, $user): File
    {
        $fileName = Str::random(10) . '.' . $data['extension'];
        $bucket_path = $path . $fileName;

        Storage::disk('s3')->put($bucket_path, file_get_contents($data['file']));

        $fileModel = new File();
        $fileModel->fill([
            'bucket_file_path' => $bucket_path,
            'original_name' => $data['name'],
            'owner_id' => $user->id
        ]);
        $fileModel->save();

        return $fileModel;
    }

    public static function getS3PrivateURL(File $file): ?string
    {
        if (Storage::disk('s3')->exists($file->bucket_file_path)) {

            return Storage::disk('s3')->temporaryUrl(
                $file->bucket_file_path,
                Carbon::now()->addMinutes(20),
                [
                    'ResponseContentDisposition' => "attachment;filename=$file->original_name"
                ]
            );
        }

        return null;
    }
}
