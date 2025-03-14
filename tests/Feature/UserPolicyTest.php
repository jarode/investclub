<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;
    
    private User $admin;
    private User $manager;
    private User $investor;
    private User $accountant;
    private User $regularUser;
    private User $newInvestor;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzenie użytkowników testowych zgodnie z wymaganiami
        $this->admin = User::factory()->create([
            'name' => 'Administrator', 
            'email' => 'admin@investclub.pl',
            'role' => 'admin',
            'verification_status' => 'verified',
        ]);
        
        $this->manager = User::factory()->create([
            'name' => 'Manager', 
            'email' => 'manager@investclub.pl',
            'role' => 'manager',
            'verification_status' => 'verified',
        ]);
        
        $this->investor = User::factory()->create([
            'name' => 'Inwestor', 
            'email' => 'investor@investclub.pl',
            'role' => 'investor',
            'verification_status' => 'verified',
        ]);
        
        $this->accountant = User::factory()->create([
            'name' => 'Księgowy', 
            'email' => 'accountant@investclub.pl',
            'role' => 'accountant',
            'verification_status' => 'verified',
        ]);
        
        $this->regularUser = User::factory()->create([
            'name' => 'Użytkownik', 
            'email' => 'user@investclub.pl',
            'role' => 'user',
            'verification_status' => 'verified',
        ]);
        
        $this->newInvestor = User::factory()->create([
            'name' => 'Nowy Inwestor', 
            'email' => 'new.investor@investclub.pl',
            'role' => 'investor',
            'verification_status' => 'unverified',
        ]);
    }
    
    #[Test]
    public function admin_can_view_users_list()
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('viewAny', User::class));
    }
    
    #[Test]
    public function manager_can_view_users_list()
    {
        $this->actingAs($this->manager);
        $this->assertTrue(Gate::allows('viewAny', User::class));
    }
    
    #[Test]
    public function investor_cannot_view_users_list()
    {
        $this->actingAs($this->investor);
        $this->assertFalse(Gate::allows('viewAny', User::class));
    }
    
    #[Test]
    public function accountant_cannot_view_users_list()
    {
        $this->actingAs($this->accountant);
        $this->assertFalse(Gate::allows('viewAny', User::class));
    }
    
    #[Test]
    public function regular_user_cannot_view_users_list()
    {
        $this->actingAs($this->regularUser);
        $this->assertFalse(Gate::allows('viewAny', User::class));
    }
    
    #[Test]
    public function admin_can_view_any_user_profile()
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $this->investor));
    }
    
    #[Test]
    public function manager_can_view_any_user_profile()
    {
        $this->actingAs($this->manager);
        $this->assertTrue(Gate::allows('view', $this->investor));
    }
    
    #[Test]
    public function user_can_view_own_profile()
    {
        $this->actingAs($this->regularUser);
        $this->assertTrue(Gate::allows('view', $this->regularUser));
    }
    
    #[Test]
    public function user_cannot_view_other_user_profile()
    {
        $this->actingAs($this->regularUser);
        $this->assertFalse(Gate::allows('view', $this->investor));
    }
    
    #[Test]
    public function admin_can_create_user()
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', User::class));
    }
    
    #[Test]
    public function regular_user_cannot_create_user()
    {
        $this->actingAs($this->regularUser);
        $this->assertFalse(Gate::allows('create', User::class));
    }
    
    #[Test]
    public function admin_can_edit_any_user()
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('update', $this->investor));
    }
    
    #[Test]
    public function manager_cannot_edit_admin()
    {
        $this->actingAs($this->manager);
        $this->assertFalse(Gate::allows('update', $this->admin));
    }
    
    #[Test]
    public function manager_can_edit_regular_user()
    {
        $this->actingAs($this->manager);
        $this->assertTrue(Gate::allows('update', $this->regularUser));
    }
    
    #[Test]
    public function user_can_edit_own_profile()
    {
        $this->actingAs($this->regularUser);
        $this->assertTrue(Gate::allows('update', $this->regularUser));
    }
}
