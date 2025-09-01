<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert string boolean values to actual booleans
        $booleanFields = ['published', 'is_top_selling', 'has_discount', 'is_subscription'];
        
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                // Convert various truthy/falsy values to proper booleans
                if (is_string($value)) {
                    $value = in_array(strtolower($value), ['true', '1', 'on', 'yes']);
                }
                $this->merge([$field => (bool) $value]);
            } else {
                // If field is not present (unchecked checkbox), set to false
                $this->merge([$field => false]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $rules['name']          = 'required|max:255';
        // $rules['specifications']          = 'required';
        // $rules['category_ids']  = 'required';
        // $rules['category_id']   = ['required', Rule::in($this->category_ids)];
        // $rules['unit']         = 'sometimes|required';
        $rules['min_qty']      = 'sometimes|required|numeric';
        $rules['unit_price']    = 'sometimes|required|numeric|gt:0';

        // if ($this->get('discount_type') == 'amount') {
        //     $rules['discount'] = 'sometimes|required|numeric|lt:unit_price';
        // } else {
        //     $rules['discount'] = 'sometimes|required|numeric|lt:100';
        // }

        $rules['current_stock'] = 'sometimes|required|numeric';
        // $rules['starting_bid']  = 'sometimes|required|numeric|min:1';
        $rules['auction_date_range']  = 'sometimes|required';

        // --- NEW: PRODUCT TYPE & BUNDLES ---
        $rules['type']                 = ['required', Rule::in(['simple', 'bundle', 'subscription', 'service', 'package', 'session'])];

        // Only when type=bundle
        $rules['bundle_items']              = 'nullable|array';
        $rules['bundle_items.*.product_id'] = 'required_if:type,bundle|exists:products,id';
        $rules['bundle_items.*.quantity'] = 'required_if:type,bundle|integer|min:1';
        
        // Boolean fields
        $rules['published'] = 'sometimes|boolean';
        $rules['is_top_selling'] = 'sometimes|boolean';
        $rules['has_discount'] = 'sometimes|boolean';
        $rules['is_subscription'] = 'sometimes|boolean';
        $rules['loyalty_points'] = 'sometimes|integer|min:0';
        
        return $rules;
    }

    /**
     * Get the validation messages of rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'             => translate('Product name is required'),
            // 'category_ids.required'     => translate('Product category is required'),
            // 'category_id.required'      => translate('Main Category is required'),
            // 'category_id.in'            => translate('Main Category must be within selected categories'),
            // 'unit.required'             => translate('Product unit is required'),
            'min_qty.required'          => translate('Minimum purchase quantity is required'),
            'min_qty.numeric'           => translate('Minimum purchase must be numeric'),
            'unit_price.required'       => translate('Unit price is required'),
            'unit_price.numeric'        => translate('Unit price must be numeric'),
            // 'discount.required'         => translate('Discount is required'),
            // 'discount.numeric'          => translate('Discount must be numeric'),
            'discount.lt'               => translate('Discount should be less than unit price'),
            'current_stock.required'    => translate('Current stock is required'),
            'current_stock.numeric'     => translate('Current stock must be numeric'),
            // 'starting_bid.required'     => translate('Starting Bid is required'),
            // 'starting_bid.numeric'      => translate('Starting Bid must be numeric'),
            // 'starting_bid.required'     => translate('Minimum Starting Bid is 1'),
            'auction_date_range.required' => translate('Auction Date Range is required'),
            'published.boolean' => translate('Published must be true or false'),
            'is_top_selling.boolean' => translate('Top selling must be true or false'),
            'has_discount.boolean' => translate('Has discount must be true or false'),
            'is_subscription.boolean' => translate('Subscription must be true or false'),
            'loyalty_points.integer' => translate('Loyalty points must be a number'),
            'loyalty_points.min' => translate('Loyalty points cannot be negative'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.*
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        // dd($this->expectsJson());
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->all(),
                'result' => false
            ], 422));
        } else {
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }
}
