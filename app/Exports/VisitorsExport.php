<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitorsExport implements FromQuery, WithHeadings , ShouldQueue
{
    use Exportable;
   
    private $limit; 
    public $table; 
    public $whereSql;
    public $fileterModel; 
    public function __construct($fileterModel){
        $this->fileterModel = $fileterModel;  
    }

    public function query()
    { 
        return $this->newCustomQuery();  
    }

    public function headings(): array
    {

         
        return 
        [ 
        'BookingId',
        'Venue Country', 
        'Venue Address', 
        'Venue Date',
        'Slot Time',
        'Sahib-e-Dua Name',
        'Venue State',
        'Venue City',
        'FirstName',
        'Last Name',
        'countryCode',
        'Phone', 
        'Email',
        'WhatsApp',
        'User Ip',
        'Booking UniqueCode',
        'Meeting Type',
        'Meeting StartAt',
        'Meeting EndsAt',
        'Sms SentAt',
        'Email SentAt',
        'Meeting ConfirmedAt',
        'BookingId',
        'Meeting Total Time (In Seconds)' 
       ];
    }


    public function CustomQuery(){

        if(!empty($this->whereSql)){
           return DB::table($this->table)->WhereRaw($this->whereSql)->select('*'); 
         }else{
           return DB::table($this->table)->select('*'); 
         } 

    }

    public function newCustomQuery(){
        $columnToTableMapping = [ 
            'booking_number' => 'vistors',
            'country_name' => 'venues',
            'address' => 'venue_addresses',
            'venue_date' => 'venue_addresses',
            'slot_time' => 'venues_sloting',
            'name' => 'users',
            'state' => 'venue_addresses',
            'city' => 'venue_addresses',
            'fname' => 'vistors',
            'lname' => 'vistors',
            'country_code' => 'vistors',
            'phone' => 'vistors',
            'email' => 'vistors',
            'is_whatsapp' => 'vistors',
            'user_ip' => 'vistors',
            'booking_uniqueid' => 'vistors',
            'meeting_type' => 'vistors',
            'meeting_start_at' => 'vistors',
            'meeting_ends_at' => 'vistors', 
            'sms_sent_at' => 'vistors',
            'email_sent_at' => 'vistors',
            'confirmed_at' => 'vistors',
            'id' => 'vistors',
            'meeting_total_time' => 'vistors',  
            'slot_id' => 'vistors',
            'user_status' => 'vistors',
 
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
    if ($this->fileterModel) {
        foreach ($this->fileterModel as $columnName => $filter) {
            if (!empty($filter['filter'])) {
                $table = $columnToTableMapping[$columnName];
                $filterValue = $filter['filter'];
                $exportArr[$columnName] = $filterValue; 
                // Use the table alias or table name in the where clause
                $query->where($table . '.' . $columnName, 'like', '%' . $filterValue . '%');
            }
        }
    }
    return $query->select($selectExpressions);
    }

}
