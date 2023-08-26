<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Validator;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = auth()->userOrFail();
            $items = DB::table('checklists')
                        ->join('checklist_items', 'checklists.id', '=', 'checklist_items.checklist_id')
                        ->get();

            return response()->json([
                'checklist_items' => $items
            ]);

        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        }
    }

    /**
     * Get list of items.
     *
     * @return \Illuminate\Http\Response
     */
    public function getItemByChecklistId($checklist_id, $item_id)
    {
        try {
            $user = auth()->userOrFail();
            $items = DB::table('checklist_items')
                        ->where('checklist_id', '=', $checklist_id)
                        ->where('id', '=', $item_id)
                        ->get();

            return response()->json([
                'checklist_items' => $items
            ]);

        } 
        catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->userOrFail();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'order' => 'required',
                'description' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = ucwords(strtolower($request->input('name')));
                $order = $request->input('order');
                $description = $request->input('description');
                $status = $request->input('status');

                $user = DB::table('checklist_items')->insert([
                    'checklist_id' => 1,
                    'name' => $name,
                    'order' => $order,
                    'description' => $description,
                    'status' => $status,
                    'created_at' => now()
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created item.', 'intended_url' => '/user'));
            }
        } 
        catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        } 
        catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $checklist_id, $item_id)
    {
        try {
            $user = auth()->userOrFail();
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $status = $request->input('status');

                DB::table('checklist_items')
                    ->where('id', $item_id)
                    ->update(['status' => $status, 'updated_at' => now()]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated item.'));
            }

        } 
        catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        } 
        catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Rename item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rename(Request $request, $checklist_id, $item_id)
    {
        try {
            $user = auth()->userOrFail();
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');

                DB::table('checklist_items')
                    ->where('checklist_id', $checklist_id)
                    ->where('id', $item_id)
                    ->update(['name' => $name, 'updated_at' => now()]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully rename item.'));
            }

        } 
        catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        } 
        catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($checklist_id, $item_id)
    {
        //
        DB::beginTransaction();

        try {
            $user = auth()->userOrFail();
            $item = DB::table('checklist_items')
                ->where('id', $item_id)->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted item.'));
        } 
        catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'User not defined'], 403);
        } 
        catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }
}
