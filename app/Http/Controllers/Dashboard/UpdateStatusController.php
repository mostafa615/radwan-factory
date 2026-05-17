<?php

namespace App\Http\Controllers\Dashboard;

use App\OperationOrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateStatusController extends Controller
{
    public function updateStatus(Request $request)
    {
        // dd($request->all());
        try {
         
            $id = $request->id;
            // dd($id );
            if (!$id) {
                return response()->json(['success' => false, 'message' => 'ID is missing'], 400);
            }

         
            $item = OperationOrderDetail::where('operation_order_id', $id)->first();
            // dd( $item);
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

         
            $item->active = $request->active;
            // dd( $item->active);
            $item->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
           

            return response()->json(['success' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
}
