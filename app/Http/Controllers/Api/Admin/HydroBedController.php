<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HydroBed;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HydroBedController extends Controller
{
    /**
     * (11) ベッド一覧取得
     * GET /api/v1/admin/hydro_beds
     */
    public function index(Request $request)
    {
        $admin = $request->user();

        // 管理者の学校に紐づくベッド一覧を取得
        // N+1問題を避けるため with() でクラスとセンサーをロード
        $beds = HydroBed::with(['class', 'sensors'])
            ->whereHas('class', function ($query) use ($admin) {
                // Adminモデルのschoolリレーション修正済み前提
                $query->where('school_id', $admin->school_id);
            })
            ->get();

        // API仕様書の形式に整形
        $response = $beds->map(function ($bed) {
            $sensor = $bed->sensors->first();
            return [
                'id' => $bed->id,
                'class_id' => $bed->class_id,
                'class_name' => $bed->class->name ?? 'Unknown',
                'name' => $bed->name,
                'device_id' => $sensor ? $sensor->device_id : null,
                'status' => $bed->status,
            ];
        });

        return response()->json($response);
    }

    /**
     * (12) ベッド新規登録
     * POST /api/v1/admin/hydro_beds
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:50',
            'device_id' => 'required|string|max:100',
        ]);

        $result = DB::transaction(function () use ($request) {
            $bed = HydroBed::create([
                'class_id' => $request->class_id,
                'name' => $request->name,
                'status' => 'standby',
            ]);

            Sensor::create([
                'hydro_bed_id' => $bed->id,
                'device_id' => $request->device_id,
                'type' => 'meter',
                'name' => 'SwitchBot Meter',
            ]);

            return $bed;
        });

        return response()->json([
            'id' => $result->id,
            'name' => $result->name,
            'created_at' => $result->created_at,
        ], 201);
    }

    /**
     * ベッド情報の更新 (修正・削除対応)
     * PUT /api/v1/admin/hydro_beds/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'device_id' => 'required|string|max:100',
            'status' => 'nullable|string|in:active,standby',
        ]);

        $admin = $request->user();

        // 権限チェック付きで取得
        $bed = HydroBed::where('id', $id)
            ->whereHas('class', function ($query) use ($admin) {
                $query->where('school_id', $admin->school_id);
            })
            ->with('sensors')
            ->firstOrFail();

        $updatedBed = DB::transaction(function () use ($bed, $request) {
            // ベッド更新
            $bed->update([
                'name' => $request->name,
                'status' => $request->status ?? $bed->status,
            ]);

            // センサー更新
            $sensor = $bed->sensors->first();
            if ($sensor) {
                $sensor->update(['device_id' => $request->device_id]);
            } else {
                $bed->sensors()->create([
                    'device_id' => $request->device_id,
                    'type' => 'meter',
                    'name' => 'SwitchBot Meter',
                ]);
            }

            return $bed;
        });

        return response()->json([
            'id' => $updatedBed->id,
            'name' => $updatedBed->name,
            'status' => $updatedBed->status,
            'device_id' => $request->device_id,
        ]);
    }

    /**
     * ベッドの削除
     * DELETE /api/v1/admin/hydro_beds/{id}
     */
    public function destroy(Request $request, $id)
    {
        $admin = $request->user();

        $bed = HydroBed::where('id', $id)
            ->whereHas('class', function ($query) use ($admin) {
                $query->where('school_id', $admin->school_id);
            })
            ->firstOrFail();

        $bed->delete();

        return response()->json(['message' => 'HydroBed deleted successfully']);
    }
}