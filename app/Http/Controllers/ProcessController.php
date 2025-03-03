<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function linkedProcesses()
    {
        return response()->json([
            ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
            ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
        ]);
    }

    public function checkUser(Request $request)
    {
        $linkedProcesses = [
            ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
            ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
        ];

        return response()->json([
            'message' => 'Checando procesos vinculados...',
            'user_data' => $request->user()->name,
            'linked_processes' => $linkedProcesses
        ]);
    }
}
