<?php

namespace App\Actions;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class StoreAttachment
{
    /**
     * @param  string  $directory  The feature's namespace on the private disk
     *                             (e.g. "pages/attachments").
     */
    public function __invoke(UploadedFile $file, string $directory): Attachment
    {
        $path = $file->store($directory, Attachment::DISK);

        if ($path === false) {
            throw new RuntimeException('Failed to store the attachment.');
        }

        return Attachment::create([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
