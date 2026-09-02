<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'min:3', 'max:255'],
            'company_slug' => ['nullable', 'string', 'regex:/^[a-z0-9-]+$/', 'unique:companies,slug'],
            'admin_name' => ['required', 'string', 'min:3', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
            'admin_phone' => ['nullable', 'string', 'regex:/^(05[0-9]{8}|\+9665[0-9]{8})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'اسم الشركة مطلوب',
            'company_name.min' => 'اسم الشركة يجب أن يكون 3 أحرف على الأقل',
            'company_slug.regex' => 'رابط الشركة يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط',
            'company_slug.unique' => 'رابط الشركة موجود بالفعل',
            'admin_name.required' => 'اسم المدير مطلوب',
            'admin_email.required' => 'بريد المدير مطلوب',
            'admin_email.email' => 'البريد يجب أن يكون صحيح',
            'admin_email.unique' => 'البريد موجود بالفعل',
            'admin_password.required' => 'كلمة المرور مطلوبة',
            'admin_password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'admin_password.regex' => 'كلمة المرور يجب أن تحتوي على أحرف وأرقام',
            'admin_phone.regex' => 'رقم الجوال يجب أن يكون بصيغة سعودية صحيحة',
        ];
    }
}
