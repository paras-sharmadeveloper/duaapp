<?php

namespace App\Http\Controllers;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrintController extends Controller
{
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
        } catch (Exception $e) {
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
