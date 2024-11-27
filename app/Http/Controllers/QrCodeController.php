<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCodeGenerater;
use App\Models\ScanQrCode;
use Validator;
use Auth;
use Illuminate\Support\Facades\Schema;

class QrCodeController extends Controller
{
    public function addQrcode(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'type' => 'required',
            'value' => 'required',
            'foreground_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/', // Hex color validation
            'background_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'eye_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $tableColumns = Schema::getColumnListing('qr_code_generaters');

        $data = $req->all();

        $filteredData = array_filter($data, function($key) use ($tableColumns) {
            return in_array($key, $tableColumns) && $key !== 'logoImage';
        }, ARRAY_FILTER_USE_KEY);

        if (isset($data['logoImage'])) {
            $filteredData['logoImage'] = $this->handleImageUpload($data['logoImage'], 'qr_codes');
        }

        $userId = Auth::user()->id;
        $filteredData['user_id'] = $userId;
        // $qrCode = QrCodeGenerater::where('user_id', $userId)->first();


            $qrCode = QrCodeGenerater::create($filteredData);
        return $this->getQrcode();

    }

    public function handleImageUpload($image, $type)
    {
        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        // Define the path
        $path = public_path('images/' . $type . '/' . $filename);
        // Save the image to the public directory
        $image->move(public_path('images/' . $type), $filename);
        return 'images/' . $type . '/' . $filename;
    }
    public function getQrcode(){
        $userId = Auth::user()->id;
        $qrcodes = QrCodeGenerater::where('user_id',$userId)->get();
        foreach ($qrcodes as $qrcode) {
            $qrcode->logoImage = isset($qrcode->logoImage) ? url($qrcode->logoImage) : '';
        }
        return response()->json([
            'status' => true,
            'message' => "Success",
            'data' => $qrcodes,
        ]);
    }
    public function scanQrCode(Request $req)
    {
        $userId = Auth::id();

        $validator = Validator::make($req->all(), [
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        ScanQrCode::create([
            'value' => $req->value,
            'user_id' => $userId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data inserted successfully',
            'data' => [
                'value' => $req->value,
                'user_id' => $userId,
            ],
        ]);
    }






}
