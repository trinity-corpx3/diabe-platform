<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeDiscount;

class EmployeeDiscountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('rh');
    }

    public function view(User $user, EmployeeDiscount $discount): bool
    {
        return $user->isAdmin() || $user->hasRole('rh');
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('rh');
    }

    public function update(User $user, EmployeeDiscount $discount): bool
    {
        return $user->isAdmin() || $user->hasRole('rh');
    }

    public function delete(User $user, EmployeeDiscount $discount): bool
    {
        return $user->isAdmin() || $user->hasRole('rh');
    }
}
