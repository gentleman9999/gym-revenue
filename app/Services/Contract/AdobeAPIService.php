<?php

declare(strict_types=1);

namespace App\Services\Contract;

use App\Domain\Contracts\Projections\Contract;
use App\Models\File;
use App\Support\Uuid;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdobeAPIService
{
    public readonly string     $organisation_id;
    public readonly string     $technical_account_id;
    public readonly ?string    $api_key;
    public readonly ?string    $client_secret;
    public string     $token;
    public string     $asset_id;
    public string     $location;
    public string     $download_uri;
    public object      $client_info;
    public array $client_data;
    protected const STATUS_DONE = 'done';
    protected const STATUS_IN_PROGRESS = 'in progress';
    protected const AUTH_ENDPOINT = 'https://ims-na1.adobelogin.com/ims/exchange/jwt/';
    protected const ASSET_ENDPOINT = 'https://pdf-services.adobe.io/assets';
    protected const PDF_ENDPOINT = 'https://pdf-services.adobe.io/operation/documentgeneration';

    public function __construct()
    {
        $this->api_key = env('ADOBE_CLIENT_ID');
        $this->organisation_id = env('ADOBE_ORGANISATION_ID');
        $this->technical_account_id = env('ADOBE_TECHNICAL_ACCOUNT_ID');
        $this->client_secret = env('ADOBE_CLIENT_SECRET');
    }

    public function generatePDF(object $client_info): string
    {
        try {
            Log::info('generatePDF Called.');
            $this->client_info = $client_info;

            return $this->createJwtToken();
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();

            return json_encode($result);
        }
    }

    public function createJwtToken(): string
    {
        $expiration_time = (time() + 60);
        $private_key = file_get_contents(storage_path('app/adobe/private.key'));

        $payload = [
            'exp' => $expiration_time,
            'iss' => $this->organisation_id,
            'sub' => $this->technical_account_id,
            'https://ims-na1.adobelogin.com/s/ent_documentcloud_sdk' => true,
            'aud' => 'https://ims-na1.adobelogin.com/c/'.$this->api_key,
        ];

        $jwt = JWT::encode($payload, $private_key, 'RS256');

        return $this->createAccessToken($jwt);
    }

    public function createAccessToken(string $jwt_token): string
    {
        $post_input = [
            'client_id' => $this->api_key,
            'client_secret' => $this->client_secret,
            'jwt_token' => $jwt_token,
        ];
        $response = Http::asForm()->post(static::AUTH_ENDPOINT, $post_input);

        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->body());
            $this->token = $response->access_token;

            return $this->getAssetId();
        } else {
            throw new \ErrorException('Token was not created');
        }
    }

    public function getAssetId(): string
    {
        $media_type = mime_content_type(storage_path($this->client_info->getTemplatePath()));

        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => $this->api_key,
        ];
        $post_input = [
            'mediaType' => $media_type,
        ];
        $response = Http::withToken($this->token)->withHeaders($headers)->post(static::ASSET_ENDPOINT, $post_input);

        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->body());
            $this->asset_id = $response->assetID;
            $upload_uri = $response->uploadUri;

            //TODO: why does getAssetId have a side effect of uploading a file?
            return $this->uploadWordFile($upload_uri, $media_type);
        } else {
            throw new \ErrorException('Asset Id was not created');
        }
    }

    public function uploadWordFile(string $upload_uri, string $media_type): string
    {
        $client = new Client();
        $response = $client->request('PUT', $upload_uri, [
            'headers' => [
                'Content-Type' => $media_type,
            ],
            'body' => fopen(storage_path($this->client_info->getTemplatePath()), 'r'),
        ]);

        if ($response->getStatusCode() == 200) {
            return $this->convertIntoPDF();
        } else {
            throw new \ErrorException('Template file not uploaded for conversion');
        }
    }

    public function convertIntoPDF(): string
    {
        $headers = [
            'x-api-key' => env('ADOBE_CLIENT_ID'),
        ];

        $post_input = [
            'assetID' => $this->asset_id,
            'outputFormat' => 'pdf',
            'jsonDataForMerge' => $this->client_info->getJsonData(),
        ];

        $response = Http::withToken($this->token)->withHeaders($headers)->post(static::PDF_ENDPOINT, $post_input);

        if ($response->getStatusCode() == 201) {
            $location = $response->header('location');
            $this->location = $location;

            return $this->checkFileStatus();
        } else {
            throw new \ErrorException('File did not converted in PDF');
        }
    }

    public function checkFileStatus(): string
    {
        $headers = [
            'x-api-key' => $this->api_key,
        ];
        $response = Http::withToken($this->token)->withHeaders($headers)->get($this->location);

        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->body());

            if ($response->status === static::STATUS_DONE) {
                $this->download_uri = $response->asset->downloadUri;

                return $this->downloadPDF();
            } elseif ($response->status === static::STATUS_IN_PROGRESS) {
                sleep(1);

                return $this->checkFileStatus();
            } else {
                throw new \ErrorException('Cannot convert given file type');
            }
        } else {
            throw new \ErrorException('File status of converted file was not found');
        }
    }

    public function downloadPDF(): string
    {
        $headers = [
            'x-api-key' => $this->api_key,
        ];
        $response = Http::withHeaders($headers)->get($this->download_uri);

        if ($response->getStatusCode() == 200) {
            $this->client_data = $this->client_info->getData();

            $file_name = $this->client_info->getFileName();
            $file_key = $this->client_info->getFilePath();
            $upload_status = Storage::disk('s3')->put($file_key, file_get_contents($this->download_uri));

            if ($upload_status) {
                //Store file data in File table.
                $file_table_data['id'] = Uuid::get();
                $file_table_data['client_id'] = $this->client_data['client_id'];
                $file_table_data['user_id'] = $this->client_data['user_id'];
                $file_table_data['filename'] = $file_name;
                $file_table_data['original_filename'] = $file_name;
                $file_table_data['extension'] = 'pdf';
                $file_table_data['url'] = '';
                $file_table_data['key'] = $file_key;
                $file_table_data['size'] = $response->header('Content-Length');
                $file_table_data['bucket'] = 's3';
                $file_table_data['fileable_type'] = Contract::class;
                $file_table_data['fileable_id'] = $this->client_data['entity_id'];
                $file_table_data['is_hidden'] = false;
                $file_table_data['type'] = 'unsigned';

                $file = File::create($file_table_data);
                $file->save();

                $result['status'] = true;
                $result['message'] = 'PDF created successfully';

                return json_encode($result);
            } else {
                throw new \ErrorException('Unable to upload file in s3 bucket');
            }
        } else {
            throw new \ErrorException('PDF was not downloaded successfully');
        }
    }
}
