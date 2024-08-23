<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UserEditRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required | max:255',
            // 'new_password' => 'nullable | confirmed',
            // 'new_password_confirmation' => 'nullable',
        ];
    }

    /**
     * パスワードと確認用パスワードが不一致の場合のエラーメッセージ
     * 
     * @return array
     */
    // public function pass_conf_err_msg() {
    //     return [
    //         'new_password.confirmed' => 'パスワードが異なります。'
    //     ];
    // }
}
