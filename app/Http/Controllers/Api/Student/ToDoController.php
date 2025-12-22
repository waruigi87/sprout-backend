<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\ToDoItem;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    /**
     * (8) ToDoチェック更新
     * PATCH /api/v1/classes/{id}/todos/{todo_item_id}
     */
    public function update(Request $request, $id, $todo_item_id)
    {
        $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        // クラスIDとToDoIDが一致するものだけ更新（セキュリティ）
        $todo = ToDoItem::where('id', $todo_item_id)
            ->where('class_id', $id)
            ->firstOrFail();

        $todo->update([
            'is_completed' => $request->is_completed,
        ]);

        return response()->json([
            'id' => $todo->id,
            'is_completed' => $todo->is_completed,
            'updated_at' => $todo->updated_at,
        ]);
    }
}