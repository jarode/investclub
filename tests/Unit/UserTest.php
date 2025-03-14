<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_has_role_attribute()
    {
        $user = User::factory()->create([
            'role' => 'investor'
        ]);

        $this->assertEquals('investor', $user->role);
        
        $user->role = 'admin';
        $user->save();
        
        $this->assertEquals('admin', $user->role);
    }

    #[Test]
    public function user_has_verification_status_attribute()
    {
        $user = User::factory()->create([
            'verification_status' => 'unverified'
        ]);

        $this->assertEquals('unverified', $user->verification_status);
        
        $user->verification_status = 'verified';
        $user->save();
        
        $this->assertEquals('verified', $user->verification_status);
    }

    #[Test]
    public function user_has_wallet_balance_attribute()
    {
        $user = User::factory()->create([
            'wallet_balance' => 100.50
        ]);

        $this->assertEquals(100.50, $user->wallet_balance);
    }

    #[Test]
    public function user_has_kyc_status_attribute()
    {
        $user = User::factory()->create([
            'kyc_status' => 'pending'
        ]);

        $this->assertEquals('pending', $user->kyc_status);
    }

    #[Test]
    public function user_can_check_if_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $investor = User::factory()->create(['role' => 'investor']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($investor->isAdmin());
    }

    #[Test]
    public function user_can_check_if_is_verified()
    {
        $verified = User::factory()->create(['verification_status' => 'verified']);
        $unverified = User::factory()->create(['verification_status' => 'unverified']);

        $this->assertTrue($verified->isVerified());
        $this->assertFalse($unverified->isVerified());
    }
    
    #[Test]
    public function user_can_check_if_has_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $investor = User::factory()->create(['role' => 'investor']);
        
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($manager->hasRole('manager'));
        $this->assertTrue($investor->hasRole('investor'));
        
        $this->assertFalse($admin->hasRole('manager'));
        $this->assertFalse($manager->hasRole('investor'));
        $this->assertFalse($investor->hasRole('admin'));
    }
    
    #[Test]
    public function user_can_check_if_has_any_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $investor = User::factory()->create(['role' => 'investor']);
        $user = User::factory()->create(['role' => 'user']);
        
        $this->assertTrue($admin->hasAnyRole(['admin', 'manager']));
        $this->assertTrue($manager->hasAnyRole(['manager', 'accountant']));
        $this->assertTrue($investor->hasAnyRole(['investor', 'admin']));
        
        $this->assertFalse($user->hasAnyRole(['admin', 'manager', 'investor']));
        $this->assertFalse($investor->hasAnyRole(['admin', 'manager']));
        $this->assertFalse($manager->hasAnyRole(['admin', 'accountant']));
    }
}
