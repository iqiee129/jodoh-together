<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WeddingDetail;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Vendor;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Test User
        $user = User::create([
            'name' => 'Demo Couple',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Add their Wedding Details
        WeddingDetail::create([
            'user_id' => $user->id,
            'partner_name' => 'Alex',
            'wedding_date' => '2026-12-25',
            'venue' => 'Grand Ballroom KL',
            'theme' => 'Modern Minimalist',
            'estimated_guests' => 300,
            'total_budget' => 50000.00,
        ]);

        // 3. Add Some Tasks
        Task::create(['user_id' => $user->id, 'title' => 'Book Photographer', 'category' => 'Media', 'deadline' => '2026-08-01', 'status' => 'pending']);
        Task::create(['user_id' => $user->id, 'title' => 'Finalize Guest List', 'category' => 'Planning', 'deadline' => '2026-09-15', 'status' => 'completed']);

        // 4. Add Some Budget Expenses
        Expense::create(['user_id' => $user->id, 'name' => 'Venue Deposit', 'category' => 'Venue', 'amount' => 5000.00, 'date' => '2026-06-01', 'status' => 'paid']);
        Expense::create(['user_id' => $user->id, 'name' => 'Catering Downpayment', 'category' => 'Food', 'amount' => 2500.00, 'date' => '2026-06-15', 'status' => 'pending']);

        // 5. Add a Global Vendor
        Vendor::create([
            'name' => 'KL Moments Photography', 
            'category' => 'Photography', 
            'state' => 'Selangor', 
            'price_tier' => '$$$', 
            'contact_info' => 'contact@klmoments.com', 
            'is_active' => true
        ]);
    }
}
