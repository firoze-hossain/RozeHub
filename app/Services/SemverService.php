<?php
namespace App\Services;
use InvalidArgumentException;
class SemverService {
    public function normalize(string $version): string {
        $v=trim($version); if (str_starts_with($v,'v')) $v=substr($v,1);
        if (!preg_match('/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-([0-9A-Za-z.-]+))?(?:\+[0-9A-Za-z.-]+)?$/',$v,$m)) throw new InvalidArgumentException("Invalid semantic version: {$version}");
        return $m[1].'.'.$m[2].'.'.$m[3].(isset($m[4]) && $m[4] !== '' ? '-'.$m[4] : '');
    }
    public function compare(string $a,string $b): int { return version_compare($this->normalize($a),$this->normalize($b)); }
    public function satisfies(string $version, ?string $constraint): bool {
        if (!$constraint || trim($constraint)==='*') return true; $v=$this->normalize($version); $c=trim($constraint);
        if (str_starts_with($c,'^')) { $base=$this->normalize(substr($c,1)); [$M,$m,$p]=array_map('intval',explode('.',preg_replace('/-.*/','',$base))); $upper=($M>0?($M+1).'.0.0':($m+1).'.0.0'); return version_compare($v,$base)>=0 && version_compare($v,$upper)<0; }
        if (str_starts_with($c,'~')) { $base=$this->normalize(ltrim(substr($c,1))); [$M,$m]=array_map('intval',array_slice(explode('.',$base),0,2)); $upper=$M.'.'.($m+1).'.0'; return version_compare($v,$base)>=0 && version_compare($v,$upper)<0; }
        if (preg_match('/^(>=|<=|>|<|=)?\s*(.+)$/',$c,$m)) { $op=$m[1]?:'='; $target=$this->normalize($m[2]); return version_compare($v,$target,$op); }
        return $this->normalize($c)===$v;
    }
}
