<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgGridManagement extends Controller
{
    public function getDataMessageLog(Request $request)
    {
        $columnToTableMapping = [ 
            'id' => 'vistors',
            'fname' => 'vistors',
            'lname' => 'vistors',
            'email' => 'vistors',
            'phone' => 'vistors',
            'booking_number' => 'vistors',
            'is_whatsapp' => 'vistors',
            'user_ip' => 'vistors',
            'country_code' => 'vistors',
            'booking_uniqueid' => 'vistors',
            'slot_id' => 'vistors',
            'meeting_type' => 'vistors',
            'sms_sent_at' => 'vistors',
            'email_sent_at' => 'vistors',
            'confirmed_at' => 'vistors',
            'user_status' => 'vistors',
            'meeting_start_at' => 'vistors',
            'meeting_ends_at' => 'vistors', 
            'country_name' => 'venues',
            'address' => 'venue_addresses',
            'venue_date' => 'venue_addresses',
            'state' => 'venue_addresses',
            'city' => 'venue_addresses',
            'slot_time' => 'venues_sloting',
            'name' => 'users'


        ];

        $query = DB::table('vistors')
        ->join('venues_sloting', 'venues_sloting.id', '=', 'vistors.slot_id')
        ->join('venue_addresses', 'venue_addresses.id', '=', 'venues_sloting.venue_address_id')
        ->join('users', 'users.id', '=', 'venue_addresses.therapist_id')
        ->join('venues', 'venue_addresses.venue_id', '=', 'venues.id');

    // Build an array of select expressions based on the mapping
    $selectExpressions = [];
    foreach ($columnToTableMapping as $columnName => $tableName) {
        $selectExpressions[] = "$tableName.$columnName AS $columnName";
    }
    $query->select($selectExpressions);
        // AG-Grid specific query parameters
        $pageSize = $request->input('pageSize', 200); // Default page size is 10
        $pageNumber = $request->input('pageNumber', 1); // Default page number is 1
        $skipCount = ($pageNumber - 1) * $pageSize; 

        if ($request->has('filterModel')) {
            foreach ($request->input('filterModel') as $columnName => $filter) {
                if (!empty($filter['filter'])) {
                    $table = $columnToTableMapping[$columnName];
                    $filterValue = $filter['filter'];

                    // Use the table alias or table name in the where clause
                    $query->where($table . '.' . $columnName, 'like', '%' . $filterValue . '%');
                }
            }
        }

        if ($request->has('sortModel')) {
            foreach ($request->input('sortModel') as $sort) {
                $columnName = $sort['colId']; // The name of the column to sort
                $sortDirection = $sort['sort']; // 'asc' or 'desc'
        
                // Determine the associated table and apply sorting
                $table = $columnToTableMapping[$columnName];
                $query->orderBy($table . '.' . $columnName, $sortDirection);
            }
        }
        

        // Get the total count of records (before filtering)
        $totalCount = $query->count();

        // Apply pagination
        $query->skip($skipCount)->take($pageSize);

        // Execute the query
        $data = $query->get();

        return response()->json([
            'rows' => $data,
            'totalRows' => $totalCount, // Rename 'recordsTotal' to 'total' for AG-Grid
        ], 200)->header('Content-Type', 'application/json');
    }

}
