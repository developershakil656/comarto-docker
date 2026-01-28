<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('id', $keyword)
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('number', 'LIKE', "%$keyword%");
            });
        }

        // date-range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // location_id
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // has_business => 1 or 0
        if ($request->has_business !== null) {
            if ($request->has_business == 1) {
                $query->whereHas('business');
            } elseif ($request->has_business == 0) {
                $query->whereDoesntHave('business');
            }
        }

        // pagination
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return success_response('users fetched successfully', [
            "data" => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ]
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        if (!in_array($request->status, ['active', 'blocked'])) {
            return error_response('Invalid status value. Allowed: active, blocked');
        }

        $user = User::find($id);
        if (!$user) {
            return error_response('User not found');
        }

        $user->update(['status' => $request->status]);

        return success_response('User status updated successfully', $user);
    }
}
