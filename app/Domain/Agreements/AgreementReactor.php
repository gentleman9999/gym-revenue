<?php

declare(strict_types=1);

namespace App\Domain\Agreements;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementSigned;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Users\Actions\UpdateUser;
use App\Domain\Users\Models\User;
use App\Models\File;
use App\Services\Contract\AdobeAPIService;
use App\Services\Contract\ClientData;
use App\Support\Uuid;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class AgreementReactor extends Reactor implements ShouldQueue
{
    public function onAgreementCreated(AgreementCreated $event): void
    {
        $agreement = Agreement::findOrFail($event->aggregateRootUuid());
        $template_locations = json_decode($agreement->template->agreement_json);
        $location = [];
        if (isset($template_locations->locations)) {
            foreach ($template_locations->locations as $template_location) {
                $location[] = $template_location->address1 . ', ' . $template_location->address2 . ' ' . $template_location->city . ', ' . $template_location->state . '' . $template_location->zip;
            }
        }
        $locations = '<br>' . implode('<br>', $location);
        eval("\$locations = \"$locations\";"); //To pass the locations array in proper format

        $json_key = [
            'client_name',
            'template_type',
            'template_category',
            'date_created',
            'end_user_name',
            'locations',
        ];

        $client_info = new ClientData('Agreement/Unsigned', $agreement->client->name, $agreement->client_id, 'TestAgreementTemplate.docx');
        $client_info->setUserId($agreement->user_id);
        $client_info->setEntityId($event->aggregateRootUuid());
        $client_info->setTemplateCategory($agreement->category->name);
        $client_info->setDateCreated(Carbon::now()->format('Y/m/d'));
        $client_info->setUserName($event->payload['user_id']);
        $client_info->setLocation($locations);
        $client_info->setJsonData($json_key);

        if (! Cache::get('is_seeding', false)) {
            $adobe_service = new AdobeAPIService();
            $adobe_service->generatePDF($client_info);
        }
    }

    public function onAgreementSigned(AgreementSigned $event): void
    {
        $this->updateUserStatus($event);
    }

    public function onAgreementUpdated(AgreementUpdated $event): void
    {
        $this->updateUserStatus($event);
    }

    protected function updateUserStatus(AgreementSigned|AgreementUpdated $event): void
    {
        $user_type = User::determineUserType($event->payload['user_id']);
        $agreement = Agreement::findOrFail($event->aggregateRootUuid());
        $agreement->signed_at = $event->createdAt();

        if (isset($event->payload['signatureFile'])) {
            //To impose signature image on PDF
            $pdf = new FPDI('l');

            $pageCount = $pdf->setSourceFile(StreamReader::createByString(file_get_contents($event->payload['pdfUrl']), 'rb'));

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage();
                $pdf->useTemplate($template, null, null, $size['width'], $size['height'], true);
                if ($pageNo == $pageCount) {
                    $signature_file_extension = (explode('/', mime_content_type($event->payload['signatureFile']))[1]);
                    $temp_file_name = 'new'.$agreement->id;
                    $temp_image_path = public_path('temp/'.$temp_file_name.'.'.$signature_file_extension);
                    fopen($temp_image_path, "wb");
                    file_put_contents($temp_image_path, file_get_contents($event->payload['signatureFile']));
                    $pdf->Image($temp_image_path, 45, $size['height'] - 50);
                }
            }

            $temp_pdf_path = public_path('/temp/'.$temp_file_name.'.pdf');
            $pdf->Output('F', $temp_pdf_path);
            $pdf_key = $agreement->client->id.'/Agreement/Signed/'.$event->payload['fileName'].'.pdf';
            $upload_status = Storage::disk('s3')->put($pdf_key, file_get_contents($temp_pdf_path));
            $file_size = Storage::disk('s3')->size($pdf_key);

            //To save signed PDF details in file table
            if ($upload_status) {
                $file = File::whereFileableId($agreement->id)->whereType('signed')->first();
                if (isset($file)) {
                    $file->delete();
                }

                $file_table_data['id'] = Uuid::get();
                $file_table_data['client_id'] = $agreement->client->id;
                $file_table_data['user_id'] = $agreement->user->id;
                $file_table_data['filename'] = $event->payload['fileName'];
                $file_table_data['original_filename'] = $event->payload['fileName'];
                $file_table_data['extension'] = 'pdf';
                $file_table_data['url'] = '';
                $file_table_data['key'] = $pdf_key;
                $file_table_data['size'] = $file_size;
                $file_table_data['bucket'] = 's3';
                $file_table_data['fileable_type'] = Agreement::class;
                $file_table_data['fileable_id'] = $agreement->id;
                $file_table_data['hidden'] = false;
                $file_table_data['type'] = 'signed';
                File::create($file_table_data);
            }
            unlink($temp_pdf_path);
            unlink($temp_image_path);
        }

        /** Convert user type to customer/member */
        UpdateUser::run(
            User::find($event->payload['user_id']),
            [
                'user_type' => $user_type,
            ]
        );
    }
}
