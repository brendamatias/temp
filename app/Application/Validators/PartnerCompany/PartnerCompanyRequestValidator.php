<?php

namespace App\Application\Validators\PartnerCompany;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerCompanyRequestValidator
{
    public function validateCreate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'document' => 'required|string|max:18',
            'is_active' => 'boolean'
        ]);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }

    public function validateUpdate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'document' => 'required|string|max:18',
            'is_active' => 'boolean'
        ]);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }
} 