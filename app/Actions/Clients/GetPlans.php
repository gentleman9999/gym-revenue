<?php

declare(strict_types=1);

namespace App\Actions\Clients;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPlans
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "id" => ["string"], //"productID",
            "search" => ["string"], //"search query string",
            "per_page" => ["integer"], // "expected products per page" //default 10
            "current_page" => ["integer"], // "current page of paginated products",
            "feed" => ["string"], // "active FEED id"
        ];
    }

    public function handle($data, $user = null)
    {
        Log::debug("GetPlans");
        Log::debug($data);
        //TODO: make plans a real thing later. just for testing topol email builder
        return [
            'success' => true,
            'data' => [
                [
                    'id' => 0,
                    'name' => 'Bronze Tier',
                    'description' => 'Just the essentials, nothing more!',
                    'url' => 'youfit.com/plans/bronze',
                    'img_url' => 'link to an image of the product',
                    'price_with_vat' => '15.99', //without currency
                    'currency' => 'USD',
                    'price_before' => '25.99',
                    'product_feed_id' => 0,
                ],
                [
                    'id' => 1,
                    'name' => 'Silver Tier',
                    'description' => 'Use any of our locations.',
                    'url' => 'youfit.com/plans/silver',
                    'img_url' => 'link to an image of the product',
                    'price_with_vat' => '25.99', //without currency
                    'currency' => 'USD',
                    'price_before' => '35.99',
                    'product_feed_id' => 0,
                ],
                [
                    'id' => 2,
                    'name' => 'Gold Tier',
                    'description' => 'With all the other stuff too!',
                    'url' => 'youfit.com/plans/bronze',
                    'img_url' => 'link to an gold of the product',
                    'price_with_vat' => '35.99', //without currency
                    'currency' => 'USD',
                    'price_before' => '45.99',
                    'product_feed_id' => 0,
                ],
            ],
            //pagination helpers
            'from' => 0, //"from id of the resource",
            'to' => 2, //"to ide of the resource",
            'total_records' => 3,// "total records of the resource",
            'per_page' => 10,// "resource per page",
            'current_page' => 1,// "current page of the resource",
            'last_page' => 1,//"last page of the resource",
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
//        $current_user = $request->user();
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $data = $request->validated();

        return $this->handle(
            $data,
            $request->user()
        );
    }
}
