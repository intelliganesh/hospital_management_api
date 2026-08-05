<?php

namespace App\Services\Master;

use App\Models\Master\ReferralDoctor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReferralDoctorService
{
    /**
     * Get all referral doctors with pagination
     * 
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Request $request)
    {
        $query = ReferralDoctor::query();

        // Search by name or designation
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('designation', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(15);
    }

    /**
     * Get a specific referral doctor by id
     * 
     * @param int $id
     * @return ReferralDoctor
     */
    public function get(int $id): ReferralDoctor
    {
        return ReferralDoctor::findOrFail($id);
    }

    /**
     * Create a new referral doctor
     * 
     * @param Request $request
     * @return ReferralDoctor
     */
    public function create(Request $request): ReferralDoctor
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:referral_doctors,name',
            'designation' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        return ReferralDoctor::create($validated);
    }

    /**
     * Update a referral doctor
     * 
     * @param Request $request
     * @param int $id
     * @return ReferralDoctor
     */
    public function update(Request $request, int $id): ReferralDoctor
    {
        $referralDoctor = ReferralDoctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:referral_doctors,name,' . $id,
            'designation' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $referralDoctor->update($validated);

        return $referralDoctor;
    }

    /**
     * Delete a referral doctor
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $referralDoctor = ReferralDoctor::findOrFail($id);
        $referralDoctor->delete();
    }

    /**
     * Get all active referral doctors for dropdown
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function referralDoctorList()
    {
        return ReferralDoctor::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }
}
