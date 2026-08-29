<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use ZipArchive;
class MarketplacePackageInspector {
    public function inspect(UploadedFile|string $file): array {
        $path=$file instanceof UploadedFile?$file->getRealPath():$file; $type=strtolower(pathinfo($path,PATHINFO_EXTENSION));
        if (in_array($type,['zip','jar','vsix','crx'],true)) return $this->inspectZip($path);
        return ['manifest'=>null,'manifestPath'=>null,'files'=>[],'warnings'=>['Automatic manifest inspection is currently available for ZIP/JAR/VSIX packages.'],'format'=>$type?:'unknown'];
    }
    private function inspectZip(string $path): array {
        $zip=new ZipArchive(); if($zip->open($path)!==true) throw new RuntimeException('Package archive could not be opened.');
        $files=[];$manifest=null;$manifestPath=null;
        for($i=0;$i<$zip->numFiles;$i++){ $name=$zip->getNameIndex($i); if($name===false||str_contains($name,'..')) continue; $files[]=$name; $base=strtolower(basename($name)); if($base==='rozehub.json'||$base==='manifest.json'){ $raw=$zip->getFromIndex($i); if($raw!==false){$decoded=json_decode($raw,true); if(is_array($decoded)){ $manifest=$decoded; $manifestPath=$name; }}} }
        $zip->close();
        return ['manifest'=>$manifest,'manifestPath'=>$manifestPath,'files'=>array_slice($files,0,500),'warnings'=>$manifest?[]:['Missing rozehub.json manifest.'],'format'=>'zip'];
    }
}
