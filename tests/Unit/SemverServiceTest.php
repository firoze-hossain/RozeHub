<?php
namespace Tests\Unit;
use App\Services\SemverService;
use PHPUnit\Framework\TestCase;
class SemverServiceTest extends TestCase {
 public function test_exact_and_ranges(): void { $s=new SemverService(); $this->assertTrue($s->satisfies('1.2.3','1.2.3')); $this->assertTrue($s->satisfies('1.5.0','^1.2.0')); $this->assertFalse($s->satisfies('2.0.0','^1.2.0')); $this->assertTrue($s->satisfies('1.2.9','~1.2.0')); $this->assertTrue($s->satisfies('2.0.0','>=1.9.0')); }
 public function test_normalization(): void { $this->assertSame('1.2.3',$this->app()->normalize('v1.2.3')); }
 private function app(): SemverService { return new SemverService(); }
}
