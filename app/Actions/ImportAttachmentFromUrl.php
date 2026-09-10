<?php

namespace App\Actions;

use App\Models\Attachment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class ImportAttachmentFromUrl
{
    /**
     * Download a legacy file and record it as an attachment. The attachment's
     * id is a name-based UUID derived from the source URL, so an already
     * imported URL resolves to its existing attachment and reruns stay
     * idempotent without any bookkeeping.
     *
     * @param  string  $directory  The feature's namespace on the private disk
     *                             (e.g. "pages/attachments").
     */
    public function __invoke(string $url, string $directory): Attachment
    {
        $url = rawurldecode($url);

        $id = Uuid::uuid5(Uuid::NAMESPACE_URL, $url)->toString();

        return Attachment::find($id)
            ?? $this->import($id, $url, $directory);
    }

    protected function import(string $id, string $url, string $directory): Attachment
    {
        // A redirect here is the legacy app's auth wall bouncing us to the
        // login page; following it would store that page as the file.
        $response = Http::withoutRedirecting()
            ->when(config('services.legacy.export_token'), fn (PendingRequest $request, string $token) => $request->withToken($token))
            ->get(str($url)->replace(' ', '%20'));

        if (! $response->successful()) {
            throw new RuntimeException("Failed to download [{$url}] (HTTP {$response->status()}).");
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'attachment');

        file_put_contents($temporaryPath, $response->body());

        try {
            $file = new File($temporaryPath);

            $path = Storage::disk(Attachment::DISK)->putFile($directory, $file);

            if ($path === false) {
                throw new RuntimeException("Failed to store the downloaded file [{$url}].");
            }

            return Attachment::forceCreate([
                'id' => $id,
                'path' => $path,
                'name' => rawurldecode(basename(uri($url)->path())) ?: 'file',
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize(),
            ]);
        } finally {
            unlink($temporaryPath);
        }
    }
}
