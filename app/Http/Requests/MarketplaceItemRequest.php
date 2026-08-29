<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceItemRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    protected function prepareForValidation(): void {
        $this->merge([
            'slug' => $this->filled('slug') ? trim((string)$this->input('slug')) : null,
            'item_id' => trim((string)$this->input('item_id')),
        ]);
    }
    public function rules(): array {
        return [
            'software_project_id'=>['required','integer','exists:software_projects,id'],
            'item_type'=>['required','string','max:30'], 'name'=>['required','string','max:160'],
            'slug'=>['nullable','string','max:120'], 'item_id'=>['required','string','max:160','regex:/^[A-Za-z0-9._:-]+$/'],
            'vendor'=>['nullable','string','max:160'], 'category'=>['nullable','string','max:100'], 'icon_path'=>['nullable','string','max:255'],
            'website'=>['nullable','url','max:255'], 'support_url'=>['nullable','url','max:255'], 'repository_url'=>['nullable','url','max:255'],
            'license'=>['nullable','string','max:80'], 'summary'=>['nullable','string','max:500'], 'description'=>['nullable','string','max:30000'],
            'permissions_text'=>['nullable','string','max:10000'], 'capabilities_text'=>['nullable','string','max:10000'],
            'compatibility_text'=>['nullable','string','max:10000'], 'minimum_project_version'=>['nullable','string','max:80'],
            'is_official'=>['nullable','boolean'],'is_verified'=>['nullable','boolean'],'is_published'=>['nullable','boolean'],
        ];
    }
}
