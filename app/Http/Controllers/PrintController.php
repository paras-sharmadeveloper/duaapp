<?php

namespace App\Http\Controllers;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PrintController extends Controller
{

    public function sendBookingUniqueId()
    {
        // Step 1: Retrieve booking_uniqueid from the database for the given date
        $bookingUniqueIds = DB::table('visitors')
            ->where('created_at', 'like', '2024-12-21%')
            ->pluck('booking_uniqueid');

        // Step 2: Loop through the booking_uniqueid and send each one to the API
        foreach ($bookingUniqueIds as $bookingUniqueId) {
            $response = $this->sendToApi($bookingUniqueId);

            // Optional: log or do something with the API response
            // Log::info($response);
        }

        return response()->json(['status' => 'Success']);
    }

    /**
     * Send the booking_uniqueid to the external API via cURL.
     *
     * @param string $bookingUniqueId
     * @return string
     */
    private function sendToApi($bookingUniqueId)
    {
        $url = 'http://205.209.108.66/api/OpenDoor?Type=0&SCode='.$bookingUniqueId.'&ReaderNo=1&ActIndex=1&OpenEvent=12&SN=20190620';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
        ]);

        // Execute the cURL request
        $response = curl_exec($curl);

        // Close the cURL session
        curl_close($curl);

        return $response;
    }




    public function printReceipt(Request $request)
    {


        // Get a list of available printers
        $printers = $this->connectedPrintersList();



        // Find the printer you want to use (e.g., by name)
        $targetPrinter = null;
        foreach ($printers as $printerName => $printerDetails) {
            echo $printerDetails;
            if ($printerDetails === 'HP LaserJet 1020') {
                $targetPrinter = $printerDetails;
                break;
            }
        }

        // Check if the target printer was found
        if (!$targetPrinter) {
            return "Printer not found!";
        }

        // Connect to the target printer
        $connector = new WindowsPrintConnector($targetPrinter);
        $printer = new Printer($connector);
        // dd($connector); die;
        try {

            $htmlContent = view('frontend.print-token')->render();
            // Print receipt content
            $printer->text('hello');
            // Add more text or commands as needed

            // Cut the paper (if applicable)
            $printer->cut();

            // Close the printer connection
            $printer->close();

            return "Receipt printed successfully!";
        } catch (\Exception $e) {
            return "Error printing receipt: " . $e->getMessage();
        }
    }


    private function printer_list(){
        $output = [];
        exec("wmic printer get name", $output);

        // Process the output to extract printer names
        $printers = [];
        foreach ($output as $line) {
            $printerName = trim($line);
            if (!empty($printerName) && $printerName !== 'Name') {
                $printers[] = $printerName;
            }
        }

        return $printers;

    }

    private function connectedPrintersList() {
        $output = [];
        exec("wmic printer where local='TRUE' get name", $output);

        // Process the output to extract printer names
        $printers = [];
        foreach ($output as $line) {
            $printerName = trim($line);
            if (!empty($printerName) && $printerName !== 'Name') {
                $printers[] = $printerName;
            }
        }

        return $printers;
    }
}
