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

        $beds = HydroBed::with(['class', 'sensors'])
            ->whereHas('class', function ($query) use ($admin) {
                $query->where('school_id', $admin->school_id);
            })
            ->get();

        $response = $beds->map(function ($bed) {
            $sensor = $bed->sensors->first();
            return [
                'id' => $bed->id,
                'class_id' => $bed->class_id,
                'class_name' => $bed->class->name ?? 'Unknown',
                'name' => $bed->name,
                'device_id' => $sensor ? $sensor->device_id : null,
                'status' => $bed->status,
                'location' => $bed->location, // ▼ 追加: 一覧にも場所を含める
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
            'location' => 'nullable|string|max:100', // ▼ 追加: バリデーション
        ]);

        $result = DB::transaction(function () use ($request) {
            $bed = HydroBed::create([
                'class_id' => $request->class_id,
                'name' => $request->name,
                'status' => 'standby',
                'location' => $request->location, // ▼ 追加: DB保存
            ]);

            Sensor::create([
                'hydro_bed_id' => $bed->id,
                'device_id' => $request->device_id,
                'type' => 'meter',
                'name' => 'SwitchBot Meter',
            ]);

            return $bed;
        });

        // ▼ 修正: フロントエンドの表示更新に必要な全データを返す
        return response()->json([
            'id' => $result->id,
            'class_id' => $result->class_id,     // 追加
            'name' => $result->name,
            'device_id' => $request->device_id,  // 追加
            'location' => $result->location,     // 追加
            'status' => $result->status,         // 追加
            'created_at' => $result->created_at,
        ], 201);
    }

    /**
     * ベッド情報の更新
     * PUT /api/v1/admin/hydro_beds/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'device_id' => 'required|string|max:100',
            'location' => 'nullable|string|max:100', // ▼ 追加
            'status' => 'nullable|string|in:active,standby',
        ]);

        $admin = $request->user();

        $bed = HydroBed::where('id', $id)
            ->whereHas('class', function ($query) use ($admin) {
                $query->where('school_id', $admin->school_id);
            })
            ->with('sensors')
            ->firstOrFail();

        $updatedBed = DB::transaction(function () use ($bed, $request) {
            $bed->update([
                'name' => $request->name,
                'location' => $request->location, // ▼ 追加: 更新処理
                'status' => $request->status ?? $bed->status,
            ]);

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

        // ▼ 修正: 更新後の完全なデータを返す
        return response()->json([
            'id' => $updatedBed->id,
            'class_id' => $updatedBed->class_id, // 追加
            'name' => $updatedBed->name,
            'status' => $updatedBed->status,
            'location' => $updatedBed->location,  // 追加
            'device_id' => $request->device_id,
        ]);
    }

    public function show(Request $request, $id)
    {
        $admin = $request->user();

        // 所属校のベッドのみ取得できるように制限
        $bed = HydroBed::with(['class', 'sensors'])
            ->where('id', $id)
            ->whereHas('class', function ($query) use ($admin) {
                $query->where('school_id', $admin->school_id);
            })
            ->firstOrFail();

        // センサーIDを取得（存在する場合）
        $sensor = $bed->sensors->first();

        // フロントエンドが期待する形式で返す
        return response()->json([
            'id' => $bed->id,
            'class_id' => $bed->class_id,
            'class_name' => $bed->class->name ?? 'Unknown',
            'name' => $bed->name,
            'device_id' => $sensor ? $sensor->device_id : null,
            'status' => $bed->status,
            'location' => $bed->location,
            'created_at' => $bed->created_at,
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