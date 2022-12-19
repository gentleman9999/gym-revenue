<?php

declare(strict_types=1);

namespace App\Services\Contract;

class ClientData
{
    public const KEY_CLIENT_NAME = 'client_name';
    public const KEY_CLIENT_ID = 'client_id';
    public const KEY_CONTRACT_ID = 'contract_id';
    public const KEY_END_USER_ID = 'end_user_id';
    public const KEY_AGREEMENT_TEMPLATE_TYPE = 'agreement_template_type';
    public const KEY_ENTITY_ID = 'entity_id';
    public string $template_name = '';
    private array $json_data = [];
    private array $data = [
        self::KEY_CLIENT_NAME => '',
        self::KEY_AGREEMENT_TEMPLATE_TYPE => '',
        self::KEY_CLIENT_ID => '',
        self::KEY_END_USER_ID => '',
        self::KEY_ENTITY_ID => '',
    ];

    public function __construct(string $template_type, string $client_name, string $client_id, string $template_name)
    {
        $this->data[self::KEY_CLIENT_ID] = $client_id;
        $this->data[self::KEY_CLIENT_NAME] = $client_name;
        $this->data[self::KEY_AGREEMENT_TEMPLATE_TYPE] = $template_type;
        $this->template_name = $template_name;
    }

    public function setEndUserId(string $end_user_id): static
    {
        $this->data[self::KEY_END_USER_ID] = $end_user_id;

        return $this;
    }

    public function setEntityId(string $entity_id): static
    {
        $this->data[self::KEY_ENTITY_ID] = $entity_id;

        return $this;
    }

    public function getTemplatePath(): string
    {
        return 'app/adobe/templates/'.$this->template_name;
    }

    public function getFileName(): string
    {
        return sprintf(
            "%s-%s-%s",
            str_replace(' ', '', $this->data[self::KEY_CLIENT_NAME]),
            $this->data[self::KEY_AGREEMENT_TEMPLATE_TYPE],
            $this->data[self::KEY_ENTITY_ID]
        );
    }

    public function getFilePath(): string
    {
        return sprintf(
            "%s/%s/%s.%s",
            $this->data[self::KEY_CLIENT_ID],
            $this->data[self::KEY_AGREEMENT_TEMPLATE_TYPE],
            $this->getFileName(),
            'pdf'
        );
    }

    public function setJsonData($json_key): array
    {
        return $this->json_data = array_filter($this->data, function ($key) use ($json_key) {
            return in_array($key, $json_key);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getJsonData(): array
    {
        return $this->json_data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
