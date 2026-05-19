# Barangay Management Information System - Application Specification

## Overview
This specification defines the modifications and new features for the Barangay Bucandala 1 Management Information System.

---

## 1. Section Modifications

### 1.1 Core Records Section
**Change:** Remove "Officials & Staff" link

**File:** `resources/views/layouts/admin.blade.php`

**Current:**
```blade
<a class="side-link {{ $isActive('admin.officials.*') }}" href="#">
    <span>Officials & Staff</span><span class="meta">SK/BHW</span>
</a>
```

**Action:** Remove this link entirely

---

### 1.2 Peace & Order Section
**Change:** Remove "Summons / Notices" link

**File:** `resources/views/layouts/admin.blade.php`

**Current:**
```blade
<a class="side-link {{ $isActive('admin.summons.*') }}" href="#">
    <span>Summons / Notices</span><span class="meta">Docs</span>
</a>
```

**Action:** Remove this link entirely

---

### 1.3 Community Section
**Change:** Remove "Assistance / Benefits" link

**File:** `resources/views/layouts/admin.blade.php`

**Current:**
```blade
<a class="side-link {{ $isActive('admin.assistance.*') }}" href="#">
    <span>Assistance / Benefits</span><span class="meta">AICS</span>
</a>
```

**Action:** Remove this link entirely

---

### 1.4 Administration Section
**Change:** Remove "Settings" link

**File:** `resources/views/layouts/admin.blade.php`

**Current:**
```blade
<a class="side-link {{ $isActive('admin.settings.*') }}" href="#">
    <span>Settings</span><span class="meta">Config</span>
</a>
```

**Action:** Remove this link entirely

---

## 2. New Page Requirements

### 2.1 Fees / Payments Page

**Purpose:** Manage and track all financial transactions

**Route Path:** `/admin/payments`

**Route Name:** `admin.payments.index`

**Controller:** `App\Http\Controllers\Admin\PaymentController`

**Model:** `App\Models\Payment` (existing, may need enhancement)

**Database Table:** `payments` (existing)

#### Features:
1. List all payments with filters (date range, status, amount range)
2. Create new payment entry (linked to document requests or standalone)
3. View payment details
4. Edit payment records
5. Mark payment as paid/unpaid
6. Generate payment reports

#### Database Schema (if new table needed):
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_request_id BIGINT UNSIGNED NULL,
    resident_id BIGINT UNSIGNED NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    payment_type ENUM('document_fee', 'clearance', 'certification', 'other') NOT NULL,
    status ENUM('pending', 'paid', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    or_number VARCHAR(50) NULL,
    paid_at DATETIME NULL,
    collected_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (document_request_id) REFERENCES document_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL,
    FOREIGN KEY (collected_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### Controller Methods:
```php
public function index(Request $request)  // List all payments
public function create()              // Show create form
public function store(Request $request) // Store new payment
public function show(Payment $payment) // View payment details
public function edit(Payment $payment) // Show edit form
public function update(Request $request, Payment $payment) // Update payment
public function markAsPaid(Payment $payment) // Mark as paid
public function generateOR(Payment $payment) // Generate Official Receipt
```

#### View Files:
- `resources/views/admin/payments/index.blade.php`
- `resources/views/admin/payments/create.blade.php`
- `resources/views/admin/payments/show.blade.php`
- `resources/views/admin/payments/edit.blade.php`

---

### 2.2 Users & Roles Page

**Purpose:** Manage user accounts and their assigned roles

**Route Path:** `/admin/users`

**Route Name:** `admin.users.index`

**Controller:** `App\Http\Controllers\Admin\UserController`

**Model:** `App\Models\User` (existing)

**Features:**
1. List all users with filters (role, status)
2. Create new user account
3. View user details
4. Edit user information
5. Assign roles to users
6. Deactivate/reactivate user accounts
7. Reset user passwords

#### User Roles:
- `admin` - Full administrative access
- `clerk` - Document request management
- `blotter` - Blotter and case management
- `readonly` - View only access

#### Controller Methods:
```php
public function index(Request $request)  // List all users
public function create()              // Show create form
public function store(Request $request) // Create new user
public function show(User $user)      // View user details
public function edit(User $user)      // Show edit form
public function update(Request $request, User $user) // Update user
public function toggleStatus(User $user) // Activate/deactivate
public function resetPassword(User $user) // Reset password
```

#### View Files:
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/show.blade.php`
- `resources/views/admin/users/edit.blade.php`

---

### 2.3 Reports Page

**Purpose:** Generate system reports (admin only - captain access)

**Route Path:** `/admin/reports`

**Route Name:** `admin.reports.index`

**Controller:** `App\Http\Controllers\Admin\ReportController`

**Features:**
1. Resident statistics (total, demographic breakdown)
2. Financial reports (payments collected)
3. Blotter/Case statistics
4. Document request statistics
5. Activity logs summary

#### Access Control:
- **Restriction:** Only users with `role = 'admin'` AND `position = 'captain'`

#### Controller Methods:
```php
public function index(Request $request)  // Show reports dashboard
public function residents()     // Resident statistics
public function financial() // Financial reports
public function blotters()  // Blotter/case reports
public function documents() // Document request reports
public function export(Request $request, $type) // Export to PDF/Excel
```

#### View Files:
- `resources/views/admin/reports/index.blade.php`
- `resources/views/admin/reports/residents.blade.php`
- `resources/views/admin/reports/financial.blade.php`
- `resources/views/admin/reports/blotters.blade.php`
- `resources/views/admin/reports/documents.blade.php`

---

## 3. Access Control Implementation

### 3.1 Middleware for Reports Restriction

**File:** `app/Http/Middleware/EnsureUserIsCaptain.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCaptain
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user is logged in
        if (!$user) {
            return redirect('/login');
        }
        
        // Check if user is admin role
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin role required.');
        }
        
        // Check if position is captain
        // Note: Position should be stored in users table or admin profile
        // For now, we'll check a config or admin profile relationship
        
        // Check admin profile for position
        if (method_exists($user, 'adminProfile') && $user->adminProfile) {
            if ($user->adminProfile->position !== 'captain') {
                abort(403, 'Unauthorized access. Captain access required.');
            }
        } else {
            // Fallback: Check config
            if (config('app.admin_captain_email') !== $user->email) {
                abort(403, 'Unauthorized access. Captain access required.');
            }
        }
        
        return $next($request);
    }
}
```

### 3.2 Updated Route Configuration

**File:** `routes/web.php`

```php
// Add new middleware to kernel first
// In bootstrap/app.php:
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'captain' => \App\Http\Middleware\EnsureUserIsCaptain::class,
    ]);
})

// Then use in routes:
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Existing routes...
    
    // NEW: Fees / Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.markPaid');
    
    // NEW: Users & Roles
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    
    // NEW: Reports (CAPTAIN ONLY)
    Route::middleware(['captain'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/residents', [ReportController::class, 'residents'])->name('reports.residents');
        Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/reports/blotters', [ReportController::class, 'blotters'])->name('reports.blotters');
        Route::get('/reports/documents', [ReportController::class, 'documents'])->name('reports.documents');
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });
});
```

### 3.3 User Model Enhancement

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'registered_via'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Roles constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLERK = 'clerk';
    public const ROLE_BLOTTER = 'blotter';
    public const ROLE_READONLY = 'readonly';
    
    // Status constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    
    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    
    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Scope to filter by role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
    
    /**
     * Scope to filter active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
```

---

## 4. Sidebar Navigation Updates

### 4.1 Updated Administration Section

**File:** `resources/views/layouts/admin.blade.php`

**After Changes:**
```blade
{{-- ADMIN --}}
<div class="mt-3">
    <a class="section-btn" data-bs-toggle="collapse" href="#secAdmin" role="button"
       aria-expanded="{{ $openAdmin ? 'true' : 'false' }}" aria-controls="secAdmin">
        <span>Administration</span>
        <span class="chev">▾</span>
    </a>
    <div class="collapse {{ $openAdmin ? 'show' : '' }} mt-2" id="secAdmin">
        <a class="side-link {{ $isActive('admin.logs.*') }}" href="{{ route('admin.logs.index') }}">
            <span>Activity Logs</span>
        </a>
        <a class="side-link {{ $isActive('admin.users.*') }}" href="{{ route('admin.users.index') }}">
            <span>Users & Roles</span>
        </a>
        <a class="side-link {{ $isActive('admin.reports.*') }}" href="{{ route('admin.reports.index') }}">
            <span>Reports</span>
        </a>
    </div>
</div>
```

---

## 5. Implementation Checklist

### Phase 1: Navigation Cleanup
- [ ] Remove "Quick" section from sidebar
- [ ] Remove subtitle/metadata from all navigation links
- [ ] Remove Officials & Staff from Core Records
- [ ] Remove Summons / Notices from Peace & Order
- [ ] Remove Assistance / Benefits from Community
- [ ] Remove Settings from Administration

### Phase 2: Fees/Payments Module
- [ ] Create Payment model (or enhance existing)
- [ ] Create PaymentController
- [ ] Create migration for payments table (if needed)
- [ ] Create index view
- [ ] Create create view
- [ ] Create show view
- [ ] Create edit view
- [ ] Add routes

### Phase 3: Users & Roles Module
- [ ] Enhance User model with roles
- [ ] Create UserController
- [ ] Create index view
- [ ] Create create view
- [ ] Create show view
- [ ] Create edit view
- [ ] Add routes

### Phase 4: Reports Module
- [ ] Create EnsureUserIsCaptain middleware
- [ ] Register middleware in kernel
- [ ] Create ReportController
- [ ] Create index view
- [ ] Create report views
- [ ] Add routes with captain middleware

### Phase 5: Testing
- [ ] Test navigation menu
- [ ] Test payments CRUD
- [ ] Test users CRUD
- [ ] Test reports access control
- [ ] Verify captain-only access

---

## 6. Design Patterns to Follow

### Controller Pattern:
```php
class SomeController extends Controller
{
    public function index(Request $request)
    {
        $items = Model::query()
            ->with(['relations'])
            ->when($request->search, fn($q, $s) => ...)
            ->when($request->filter, fn($q, $f) => ...)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
            
        return view('admin.module.index', compact('items'));
    }
    
    public function create()
    {
        return view('admin.module.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        Model::create($validated);
        return redirect()->route('module.index')->with('success', 'Created successfully');
    }
    
    public function show(Model $item)
    {
        $item->load(['relations']);
        return view('admin.module.show', compact('item'));
    }
    
    public function edit(Model $item)
    {
        return view('admin.module.edit', compact('item'));
    }
    
    public function update(Request $request, Model $item)
    {
        $validated = $request->validate([...]);
        $item->update($validated);
        return redirect()->route('module.show', $item)->with('success', 'Updated successfully');
    }
    
    public function destroy(Model $item)
    {
        $item->delete();
        return redirect()->route('module.index')->with('success', 'Deleted successfully');
    }
}
```

### View Pattern:
- Use existing `admin.blade.php` layout
- Use `.page-surface` class for content wrapper
- Use Bootstrap 5 components
- Match existing UI styling

### Route Pattern:
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/module', [Controller::class, 'index'])->name('module.index');
    Route::get('/module/create', [Controller::class, 'create'])->name('module.create');
    Route::post('/module', [Controller::class, 'store'])->name('module.store');
    Route::get('/module/{item}', [Controller::class, 'show'])->name('module.show');
    Route::get('/module/{item}/edit', [Controller::class, 'edit'])->name('module.edit');
    Route::put('/module/{item}', [Controller::class, 'update'])->name('module.update');
    Route::delete('/module/{item}', [Controller::class, 'destroy'])->name('module.destroy');
});
```

---

## 7. Summary

This specification provides:

1. **Navigation Changes:**
   - Removed Quick section
   - Removed metadata from all links
   - Removed 4 menu items from various sections

2. **New Modules:**
   - Fees/Payments: Full CRUD with financial tracking
   - Users & Roles: User account management
   - Reports: Captain-only access to system reports

3. **Access Control:**
   - New `EnsureUserIsCaptain` middleware
   - Captain-only reports section
   - Role-based user management

All changes follow existing codebase patterns and maintain UI consistency.