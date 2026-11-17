<?php

namespace App\Http\Requests;

use App\Models\Business;
use App\OrderChannel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'channel' => ['required', Rule::enum(OrderChannel::class)],
            'delivery_address' => ['nullable', 'required_if:channel,delivery', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'idempotency_key' => ['required', 'uuid'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array:quantity'],
            'items.*.quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $business = $this->route('business');

                if (! $business instanceof Business) {
                    return;
                }

                $items = $this->input('items', []);
                $selectedItems = array_filter(
                    $items,
                    fn (array $line): bool => (int) $line['quantity'] > 0,
                );
                $itemIds = array_keys($selectedItems);

                if ($itemIds === []) {
                    $validator->errors()->add('items', 'Agrega al menos un producto al pedido.');

                    return;
                }

                foreach ($itemIds as $itemId) {
                    if (! ctype_digit((string) $itemId)) {
                        $validator->errors()->add('items', 'Uno de los productos seleccionados no es válido.');

                        return;
                    }
                }

                $availableItems = $business->menuItems()
                    ->available()
                    ->whereIn('id', $itemIds)
                    ->pluck('id')
                    ->all();

                foreach ($itemIds as $itemId) {
                    if (! in_array((int) $itemId, $availableItems, true)) {
                        $validator->errors()->add(
                            "items.$itemId.quantity",
                            'El producto seleccionado no está disponible en este menú.',
                        );
                    }
                }
            },
        ];
    }
}
