<?php

declare(strict_types=1);

namespace App\Actions\AWS\S3;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class GetFilesFromFolder
{
    use AsAction;

    protected string $folder;

    /**
     * Determine if the user is authorized to make this action.
     *
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    public function handle(string $folder, string $disk = 's3')
    {
        $file_stack = [];

        $this->folder = "{$folder}";
        $folder       = $this->folder;

        //Cache::forget('file-list-'.$this->folder);

        $result_list_objects = Cache::remember('file-list-' . $folder, 60 * 1 * 10, function () use ($folder, $disk) {
            $storage = Storage::disk($disk);
//            dd($storage->files($folder));
            $client = $storage->getAdapter()->getClient();

            $command           = $client->getCommand('ListObjects');
            $command['Bucket'] = $storage->getAdapter()->getBucket();
            $command['Prefix'] = $folder;

            return $client->execute($command);
        });

        if (! empty($result_list_objects)) {
            foreach (($result_list_objects['Contents'] ?? []) as $object_file) {
                $object_path = $object_file['Key'];
                if ($object_path != "$folder/") {
                    $temp      = explode('.', $object_path);
                    $extension = end($temp);

                    $object_date = $object_file['LastModified'];

                    $object_stamp = strtotime($object_date);

                    // @todo - check if this file has been consumed before.
                    $file_stack[$object_path] = [
                        'LastModified' => $object_stamp,
                        'Extension' => $extension,
                        'Thumbnail' => in_array($extension, ['png', 'jpg', 'webp', 'jpeg']) ? "https://" : null,
                    ];
                }
            }
        }

        $file_stack = collect($file_stack)->sortByDesc('LastModified');


        return $file_stack->toArray();
    }
}
