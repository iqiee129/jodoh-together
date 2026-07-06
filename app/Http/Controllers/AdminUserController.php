<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where(function ($q) {
                $q->where('role', '!=', 'admin')
                    ->orWhereNull('role');
            });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->get();

        $totalUsers = User::where(function ($q) {
                $q->where('role', '!=', 'admin')
                    ->orWhereNull('role');
            })
            ->count();

        $regularUsers = $totalUsers;

        $recentUsers = User::where(function ($q) {
                $q->where('role', '!=', 'admin')
                    ->orWhereNull('role');
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $adminUsers = User::where('role', 'admin')->count();

        $userDetails = [];

        foreach ($users as $user) {
            $wedding = null;

            if (Schema::hasTable('wedding_details')) {
                $wedding = DB::table('wedding_details')
                    ->where('user_id', $user->id)
                    ->first();
            }

            $totalBudget = 0;

            if ($wedding && isset($wedding->total_budget)) {
                $totalBudget = (float) ($wedding->total_budget ?? 0);
            }

            $totalSpent = 0;
            $pendingExpenses = 0;
            $vendorCount = 0;

            if (Schema::hasTable('expenses')) {
                if (Schema::hasColumn('expenses', 'amount')) {
                    $totalSpent = (float) DB::table('expenses')
                        ->where('user_id', $user->id)
                        ->sum('amount');
                }

                if (Schema::hasColumn('expenses', 'status')) {
                    $pendingExpenses = DB::table('expenses')
                        ->where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->count();
                }

                if (Schema::hasColumn('expenses', 'vendor_id')) {
                    $vendorCount = DB::table('expenses')
                        ->where('user_id', $user->id)
                        ->whereNotNull('vendor_id')
                        ->distinct('vendor_id')
                        ->count('vendor_id');
                }
            }

            $totalTasks = 0;
            $completedTasks = 0;
            $pendingTasks = 0;
            $overdueTasks = 0;

            if (Schema::hasTable('tasks')) {
                $totalTasks = DB::table('tasks')
                    ->where('user_id', $user->id)
                    ->count();

                if (Schema::hasColumn('tasks', 'status')) {
                    $completedTasks = DB::table('tasks')
                        ->where('user_id', $user->id)
                        ->where('status', 'completed')
                        ->count();

                    $pendingTasks = DB::table('tasks')
                        ->where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->count();
                }

                if (Schema::hasColumn('tasks', 'deadline') && Schema::hasColumn('tasks', 'status')) {
                    $overdueTasks = DB::table('tasks')
                        ->where('user_id', $user->id)
                        ->where('status', '!=', 'completed')
                        ->whereNotNull('deadline')
                        ->whereDate('deadline', '<', now()->toDateString())
                        ->count();
                }
            }

            $weddingDate = $wedding->wedding_date ?? null;

            $userDetails[$user->id] = [
                'id' => $user->id,
                'name' => $user->name ?? 'User',
                'email' => $user->email ?? '-',
                'role' => ucfirst($user->role ?? 'user'),
                'joined' => $user->created_at ? $user->created_at->format('d M Y') : '-',
                'updated' => $user->updated_at ? $user->updated_at->format('d M Y') : '-',

                'wedding' => [
                    'partner_name' => $wedding->partner_name ?? '-',
                    'wedding_date' => $weddingDate ? \Carbon\Carbon::parse($weddingDate)->format('d M Y') : '-',
                    'venue' => $wedding->venue ?? '-',
                    'theme' => $wedding->theme ?? '-',
                    'guest_count' => $wedding->guest_count ?? $wedding->estimated_guests ?? '-',
                ],

                'budget' => [
                    'total_budget' => $totalBudget,
                    'total_spent' => $totalSpent,
                    'remaining' => max($totalBudget - $totalSpent, 0),
                    'pending_expenses' => $pendingExpenses,
                ],

                'tasks' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'pending' => $pendingTasks,
                    'overdue' => $overdueTasks,
                ],

                'vendors' => [
                    'added' => $vendorCount,
                ],
            ];
        }

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'regularUsers',
            'recentUsers',
            'adminUsers',
            'userDetails'
        ));
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own admin account.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.users')
                ->with('error', 'Admin accounts cannot be deleted from this page.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}