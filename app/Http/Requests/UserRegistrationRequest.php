<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Enums\GenderEnum;
use App\Models\Enums\MessageChannelEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class UserRegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $channel = $this->{User::VAL_CHANNEL};
        if ($channel == MessageChannelEnum::EMAIL->value) {
            $receiverRule = 'required|email:rfc,filter';
        } else {
            $receiverRule = 'required|digits:10';
        }

        return [
            User::VAL_USER_ID => $receiverRule,
            User::VAL_CODE => 'required',
            User::VAL_CHANNEL => [new Enum(MessageChannelEnum::class)],
            User::VAL_NAME => 'required|max:40',
            User::VAL_PASSWORD => 'required|between:6,25|required_with:' . User::VAL_CONFIRM_PASSWORD,
            User::VAL_CONFIRM_PASSWORD => 'required|same:' . User::VAL_PASSWORD,
            User::VAL_BIRTHDAY => 'nullable|before_or_equal:'.\Carbon\Carbon::now()->subYears(10)->format('Y-m-d'),
            User::VAL_GENDER => [new Enum(GenderEnum::class)],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Controller::responseIER($validator->errors()->first()));
    }
}
