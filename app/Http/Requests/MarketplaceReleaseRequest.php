<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceReleaseRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'version'=>['required','string','max:80'], 'platform'=>['required','string','max:30'], 'architecture'=>['required','string','max:20'],
            'channel'=>['required','string','max:30'], 'minimum_app_version'=>['nullable','string','max:80'], 'maximum_app_version'=>['nullable','string','max:80'],
            'package_type'=>['required','string','max:30'], 'release_notes'=>['nullable','string','max:30000'],
            'is_mandatory'=>['nullable','boolean'], 'package'=>['nullable','file','max:8388608'],
            'upload_token'=>['nullable','string','regex:/^[A-Za-z0-9_-]{20,100}$/'],
        ];
    }
}
