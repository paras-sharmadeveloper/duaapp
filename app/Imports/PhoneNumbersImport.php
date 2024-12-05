<?php

namespace App\Imports;

use App\Models\WhatsAppNotificationNumbers; // Assuming you are saving the phone numbers in this model
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class PhoneNumbersImport implements ToCollection
{
    /**
     * This method processes the entire collection of rows in the CSV.
     * It will be called after the file is loaded.
     *
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Loop through each row in the collection
        foreach ($collection as $row) {
            // Assuming the CSV has 'country_code' in column 0 and 'phone' in column 1
            WhatsAppNotificationNumbers::create([
                'country_code' => $row[0],
                'phone' => $row[1],
            ]);
        }
    }
}
