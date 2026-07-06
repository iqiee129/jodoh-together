<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orWhere('state', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('state') && $request->state !== 'all') {
            $query->where('state', $request->state);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $vendors = $query->latest()->get();

        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('is_active', true)->count();
        $inactiveVendors = Vendor::where('is_active', false)->count();

        return view('admin.vendors', compact(
            'vendors',
            'totalVendors',
            'activeVendors',
            'inactiveVendors'
        ));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'category' => ['required', 'string', 'max:100'],
        'state' => ['required', 'string', 'max:100'],
        'location' => ['nullable', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'phone' => ['nullable', 'string', 'max:50'],
        'email' => ['nullable', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', 'max:255'],
        'description' => ['nullable', 'string'],
        'image_url' => [
            'nullable',
            'url',
            'max:1000',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! in_array(parse_url((string) $value, PHP_URL_SCHEME), ['http', 'https'], true)) {
                    $fail('The image URL must use http or https.');
                }
            },
        ],
        'image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'remove_image' => ['nullable', 'in:0,1'],
        'is_active' => ['nullable'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    if ($request->hasFile('image_upload')) {
        $path = $request->file('image_upload')->store('vendors', 'public');
        $data['image_url'] = '/storage/' . $path;
    }

    unset($data['image_upload'], $data['remove_image']);

    $data['is_active'] = $request->has('is_active');

    Vendor::create($data);

    return redirect()->route('admin.vendors')->with('success', 'Vendor added successfully.');
}

public function update(Request $request, Vendor $vendor)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'category' => ['required', 'string', 'max:100'],
        'state' => ['required', 'string', 'max:100'],
        'location' => ['nullable', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'phone' => ['nullable', 'string', 'max:50'],
        'email' => ['nullable', 'email', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', 'max:255'],
        'description' => ['nullable', 'string'],
        'image_url' => [
            'nullable',
            'url',
            'max:1000',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! in_array(parse_url((string) $value, PHP_URL_SCHEME), ['http', 'https'], true)) {
                    $fail('The image URL must use http or https.');
                }
            },
        ],
        'image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'remove_image' => ['nullable', 'in:0,1'],
        'is_active' => ['nullable'],
    ], [
        'email.regex' => 'Please enter a valid email address, like name@example.com.',
    ]);

    $removeImage = $request->input('remove_image') === '1';
    $manualImageUrl = $data['image_url'] ?? null;

    unset($data['image_upload'], $data['remove_image']);

    if ($removeImage) {
        $this->deleteUploadedVendorImage($vendor->image_url);
        $data['image_url'] = null;
    } elseif ($request->hasFile('image_upload')) {
        $this->deleteUploadedVendorImage($vendor->image_url);

        $path = $request->file('image_upload')->store('vendors', 'public');
        $data['image_url'] = '/storage/' . $path;
    } elseif (blank($manualImageUrl)) {
        unset($data['image_url']);
    }

    $data['is_active'] = $request->has('is_active');

    $vendor->update($data);

    return redirect()->route('admin.vendors')->with('success', 'Vendor updated successfully.');
}

public function destroy(Vendor $vendor)
{
    $this->deleteUploadedVendorImage($vendor->image_url);

    $vendor->delete();

    return redirect()->route('admin.vendors')->with('success', 'Vendor deleted successfully.');
}

private function deleteUploadedVendorImage(?string $imageUrl): void
{
    if (!$imageUrl) {
        return;
    }

    $path = parse_url($imageUrl, PHP_URL_PATH);

    if (!$path || !str_starts_with($path, '/storage/vendors/')) {
        return;
    }

    $storagePath = str_replace('/storage/', '', $path);

    Storage::disk('public')->delete($storagePath);
}
}
