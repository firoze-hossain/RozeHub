<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EcosystemProfileRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->is_admin === true; }
    public function rules(): array {
        $jsonList = ['nullable','array'];
        return [
            'ecosystem_type'=>['required','string','max:40'], 'title'=>['required','string','max:180'], 'description'=>['nullable','string','max:10000'],
            'item_types'=>$jsonList, 'item_types.*'=>['string','max:60'], 'capabilities'=>$jsonList, 'capabilities.*'=>['string','max:100'],
            'package_types'=>$jsonList, 'package_types.*'=>['string','max:30'], 'platforms'=>$jsonList, 'platforms.*'=>['string','max:30'],
            'architectures'=>$jsonList, 'architectures.*'=>['string','max:20'], 'channels'=>$jsonList, 'channels.*'=>['string','max:30'],
            'integration_targets'=>$jsonList, 'integration_targets.*'=>['string','max:120'],
            'marketplace_enabled'=>['nullable','boolean'], 'community_contributions'=>['nullable','boolean'], 'moderation_required'=>['nullable','boolean'],
        ];
    }
}
