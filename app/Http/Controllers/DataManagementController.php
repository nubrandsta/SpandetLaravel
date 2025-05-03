<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Data::query();

        // Handle search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('uploader', 'like', "%{$search}%")
                  ->orWhere('thoroughfare', 'like', "%{$search}%")
                  ->orWhere('subLocality', 'like', "%{$search}%")
                  ->orWhere('locality', 'like', "%{$search}%")
                  ->orWhere('subAdmin', 'like', "%{$search}%")
                  ->orWhere('adminArea', 'like', "%{$search}%")
                  ->orWhere('postalCode', 'like', "%{$search}%");
            });
        }

        // Handle sorting
        $sortColumns = ['created_at', 'uploader', 'group', 'spandukCount', 'thoroughfare', 'subLocality', 'locality', 'subAdmin', 'adminArea', 'postalCode'];
        $sort = $request->input('sort');
        $direction = $request->input('direction');

        if ($sort && in_array($sort, $sortColumns)) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $data = $query->paginate(10)->appends($request->query());

        return view('datamanage', compact('data'));
    }

    public function destroy($id)
    {
        try {
            $data = Data::findOrFail($id);
            $data->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('data.management')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('data.management')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        try {
            // Get data with the same filters as the index method
            $query = Data::query();

            // Handle search
            if ($search = $request->input('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('uploader', 'like', "%{$search}%")
                      ->orWhere('thoroughfare', 'like', "%{$search}%")
                      ->orWhere('subLocality', 'like', "%{$search}%")
                      ->orWhere('locality', 'like', "%{$search}%")
                      ->orWhere('subAdmin', 'like', "%{$search}%")
                      ->orWhere('adminArea', 'like', "%{$search}%")
                      ->orWhere('postalCode', 'like', "%{$search}%");
                });
            }

            // Handle sorting
            $sortColumns = ['created_at', 'uploader', 'group', 'spandukCount', 'thoroughfare', 'subLocality', 'locality', 'subAdmin', 'adminArea', 'postalCode'];
            $sort = $request->input('sort');
            $direction = $request->input('direction');

            if ($sort && in_array($sort, $sortColumns)) {
                $query->orderBy($sort, $direction ?? 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Get all data (no pagination)
            $data = $query->get();
            
            // Get server IP address
            $serverIp = $request->server('SERVER_ADDR') ?: $request->server('LOCAL_ADDR') ?: '127.0.0.1';

            // Set up CSV file
            $filename = 'export-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Expires' => '0'
            ];

            // Create the CSV file
            $handle = fopen('php://temp', 'r+');
            
            // Add headers
            fputcsv($handle, ['ID', 'Waktu', 'Uploader', 'Kelompok', 'Jml Spanduk', 'Area 1', 'Area 2', 'Area 3', 'Area 4', 'Area 5', 'Kode Pos', 'Latitude', 'Longitude', 'Image URL']);
            
            // Add data rows
            foreach ($data as $item) {
                // Format image URL with spandet.my.id domain
                $imageUrl = '';
                if (!empty($item->imgURI)) {
                    $imageUrl = 'https://spandet.my.id/' . ltrim($item->imgURI, '/');
                }
                
                fputcsv($handle, [
                    $item->id,
                    $item->created_at->format('d M Y H:i:s'),
                    $item->uploader,
                    $item->group,
                    $item->spandukCount,
                    $item->thoroughfare,
                    $item->subLocality,
                    $item->locality,
                    $item->subAdmin,
                    $item->adminArea,
                    $item->postalCode,
                    $item->lat,
                    $item->long,
                    $imageUrl
                ]);
            }
            
            // Reset the file pointer to the beginning
            rewind($handle);
            
            // Get the content of the CSV
            $content = stream_get_contents($handle);
            fclose($handle);
            
            // Return the CSV file as a download
            return Response::make($content, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            return redirect()->route('data.management')
                ->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    public function exportExcel(Request $request)
    {
        try {
            // Import required PhpSpreadsheet classes
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Add title row and merge cells
            $sheet->setCellValue('A1', 'DATA MEDIA REKLAME');
            $sheet->mergeCells('A1:N1');
            
            // Style the title row
            $titleStyle = $sheet->getStyle('A1');
            $titleStyle->getFont()->setBold(true);
            $titleStyle->getFont()->setSize(16);
            $titleStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Set column headers (now in row 2)
            $sheet->setCellValue('A2', 'ID');
            $sheet->setCellValue('B2', 'Waktu');
            $sheet->setCellValue('C2', 'Uploader');
            $sheet->setCellValue('D2', 'Kelompok');
            $sheet->setCellValue('E2', 'Jml Spanduk');
            $sheet->setCellValue('F2', 'Area 1');
            $sheet->setCellValue('G2', 'Area 2');
            $sheet->setCellValue('H2', 'Area 3');
            $sheet->setCellValue('I2', 'Area 4');
            $sheet->setCellValue('J2', 'Area 5');
            $sheet->setCellValue('K2', 'Kode Pos');
            $sheet->setCellValue('L2', 'Latitude');
            $sheet->setCellValue('M2', 'Longitude');
            $sheet->setCellValue('N2', 'Image URL');
            
            // Style the header row
            $headerStyle = $sheet->getStyle('A2:N2');
            $headerStyle->getFont()->setBold(true);
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $headerStyle->getFill()->getStartColor()->setARGB('FF4F81BD'); // Blue background
            $headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); // White text
            
            // Query data with the same filters as the index method
            $query = Data::query();
            
            // Handle search
            if ($search = $request->input('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('uploader', 'like', "%{$search}%")
                      ->orWhere('thoroughfare', 'like', "%{$search}%")
                      ->orWhere('subLocality', 'like', "%{$search}%")
                      ->orWhere('locality', 'like', "%{$search}%")
                      ->orWhere('subAdmin', 'like', "%{$search}%")
                      ->orWhere('adminArea', 'like', "%{$search}%")
                      ->orWhere('postalCode', 'like', "%{$search}%");
                });
            }
            
            // Handle sorting
            $sortColumns = ['created_at', 'uploader', 'group', 'spandukCount', 'thoroughfare', 'subLocality', 'locality', 'subAdmin', 'adminArea', 'postalCode'];
            $sort = $request->input('sort');
            $direction = $request->input('direction');
            
            if ($sort && in_array($sort, $sortColumns)) {
                $query->orderBy($sort, $direction ?? 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Get all data (no pagination for export)
            $data = $query->get();
            
            // Get server IP address
            $serverIp = $request->server('SERVER_ADDR') ?: $request->server('LOCAL_ADDR') ?: '127.0.0.1';
            
            // Fill data rows
            $row = 3; // Start from row 3 since we have title and header rows
            foreach ($data as $item) {
                // Format image URL with spandet.my.id domain
                $imageUrl = '';
                if (!empty($item->imgURI)) {
                    $imageUrl = 'https://spandet.my.id/' . ltrim($item->imgURI, '/');
                }
                
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $item->created_at->format('d M Y H:i:s'));
                $sheet->setCellValue('C' . $row, $item->uploader);
                $sheet->setCellValue('D' . $row, $item->group);
                $sheet->setCellValue('E' . $row, $item->spandukCount);
                $sheet->setCellValue('F' . $row, $item->thoroughfare);
                $sheet->setCellValue('G' . $row, $item->subLocality);
                $sheet->setCellValue('H' . $row, $item->locality);
                $sheet->setCellValue('I' . $row, $item->subAdmin);
                $sheet->setCellValue('J' . $row, $item->adminArea);
                $sheet->setCellValue('K' . $row, $item->postalCode);
                $sheet->setCellValue('L' . $row, $item->lat);
                $sheet->setCellValue('M' . $row, $item->long);
                $sheet->setCellValue('N' . $row, $imageUrl);
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Create a temporary file
            $filename = 'data_export_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Return response with headers for download
            return new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            return redirect()->route('data.management')
                ->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}