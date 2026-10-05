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

    protected function prepareForValidation(): void
    {
        $listKeys = ['item_types','capabilities','package_types','platforms','architectures','channels','integration_targets'];
        $merge = [];
        foreach ($listKeys as $key) {
            $val = $this->input($key);
            if (is_array($val)) {
                $flat = [];
                foreach ($val as $item) {
                    if (is_string($item)) {
                        $lines = preg_split('/[\r\n,]+/', $item, -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($lines as $line) {
                            $trimmed = trim($line);
                            if ($trimmed !== '') {
                                $flat[] = $trimmed;
                            }
                        }
                    } elseif (is_numeric($item)) {
                        $flat[] = (string)$item;
                    }
                }
                $merge[$key] = array_values(array_unique($flat));
            } elseif (is_string($val)) {
                $lines = preg_split('/[\r\n,]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                $merge[$key] = array_values(array_unique(array_filter(array_map('trim', $lines))));
            }
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
