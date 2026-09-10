<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnalyticsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any analytics.
     */
    public function viewAny(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'clerk', 'treasurer', 'lupon']);
    }

    /**
     * Determine whether the user can view the executive dashboard.
     */
    public function viewExecutiveDashboard(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary']);
    }

    /**
     * Determine whether the user can view resident analytics.
     */
    public function viewResidentAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'clerk']);
    }

    /**
     * Determine whether the user can view household analytics.
     */
    public function viewHouseholdAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary']);
    }

    /**
     * Determine whether the user can view document request analytics.
     */
    public function viewDocumentRequestAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'clerk']);
    }

    /**
     * Determine whether the user can view payment analytics.
     */
    public function viewPaymentAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'treasurer']);
    }

    /**
     * Determine whether the user can view blotter analytics.
     */
    public function viewBlotterAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'lupon']);
    }

    /**
     * Determine whether the user can view case and hearing analytics.
     */
    public function viewCaseAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'lupon']);
    }

    /**
     * Determine whether the user can view barangay officials and terms analytics.
     */
    public function viewOfficialAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary']);
    }

    /**
     * Determine whether the user can view announcement and event analytics.
     */
    public function viewAnnouncementEventAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary', 'clerk']);
    }

    /**
     * Determine whether the user can view user and administrator analytics or audit logs.
     */
    public function viewAdminSystemAnalytics(User $user): bool
    {
        $role = $user->analytics_role;
        return in_array($role, ['captain', 'secretary']);
    }
}