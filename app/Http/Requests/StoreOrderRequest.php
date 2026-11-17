<?php

namespace App\Http\Requests;

use App\OrderChannel;
use App\OrderOrigin;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'origin' => ['required', Rule::in([OrderOrigin::Phone->value, OrderOrigin::Chat->value])],
            'delivery_address' => ['nullable', 'required_if:channel,delivery', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'idempotency_key' => ['required', 'uuid'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array:quantity'],
            'items.*.quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
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

                $availableItems = $this->user()->business->menuItems()
                    ->whereIn('id', $itemIds)
                    ->available()
                    ->pluck('id')
                    ->all();

                foreach ($itemIds as $itemId) {
                    if (! in_array((int) $itemId, $availableItems, true)) {
                        $validator->errors()->add(
                            "items.$itemId.quantity",
                            'El producto seleccionado no está disponible.',
                        );
                    }
                }
            },
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Indica el nombre del cliente.',
            'customer_phone.required' => 'Indica el teléfono del cliente.',
            'channel.required' => 'Selecciona el tipo de pedido.',
            'delivery_address.required_if' => 'Indica la dirección para el domicilio.',
            'items.required' => 'Agrega al menos un producto al pedido.',
            'items.min' => 'Agrega al menos un producto al pedido.',
            'items.*.quantity.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
