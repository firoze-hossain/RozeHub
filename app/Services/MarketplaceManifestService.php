<?php
namespace App\Services;
use App\Models\MarketplaceItem;
use Illuminate\Validation\ValidationException;
class MarketplaceManifestService {
    public const SCHEMA='1.0';
    public function validate(array $manifest, MarketplaceItem $item, array $release): array {
        $errors=[];
        foreach(['id','name','version','type'] as $key) if(empty($manifest[$key])) $errors["manifest.{$key}"]="rozehub.json requires {$key}.";
        if(isset($manifest['id']) && $manifest['id'] !== $item->item_id) $errors['manifest.id']='Manifest id must match the marketplace item ID.';
        if(isset($manifest['name']) && $manifest['name'] !== $item->name) $errors['manifest.name']='Manifest name must match the marketplace item name.';
        if(isset($manifest['version']) && $manifest['version'] !== $release['version']) $errors['manifest.version']='Manifest version must match the release version.';
        if(isset($manifest['type']) && $manifest['type'] !== $item->item_type) $errors['manifest.type']='Manifest type must match the marketplace item type.';
        if(isset($manifest['schema']) && !in_array((string)$manifest['schema'],['1.0','1'],true)) $errors['manifest.schema']='Unsupported rozehub.json schema version.';
        if($errors) throw ValidationException::withMessages($errors);
        return $manifest;
    }
    public function dependencyRows(array $manifest): array {
        $deps=$manifest['dependencies']??[]; if(!is_array($deps)) return [];
        $out=[]; foreach($deps as $id=>$constraint){ if(is_int($id) && is_array($constraint)){ $id=$constraint['id']??null; $constraint=$constraint['version']??null; } if(!$id) continue; $out[]=['itemId'=>(string)$id,'constraint'=>$constraint?trim((string)$constraint):'*','optional'=>(bool)($manifest['optionalDependencies'][$id]??false)]; } return $out;
    }
}
