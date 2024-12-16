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
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $tableColumns = Schema::getColumnListing('qr_code_generaters');

        $data = $req->all();

        $filteredData = array_filter($data, function($key) use ($tableColumns) {
            return in_array($key, $tableColumns) && $key !== 'logoImage';
        }, ARRAY_FILTER_USE_KEY);

        if ($req->hasFile('value') && $req->file('value')->isValid()) {
            $filteredData['value'] = $this->handleImageUpload($req->file('value'), 'qr_files');
        }
        
        if (isset($data['logoImage'])) {
            $filteredData['logoImage'] = $this->handleImageUpload($data['logoImage'], 'qr_codes');
        }

        $userId = Auth::user()->id;
        $filteredData['user_id'] = $userId;
        // $qrCode = QrCodeGenerater::where('user_id', $userId)->first();


            $qrCode = QrCodeGenerater::create($filteredData);
            $qrCode->logoImage = isset($qrCode->logoImage) ? url($qrCode->logoImage) : '';
             return response()->json([ 'status' => true, 'message' => "Qr code added successfully", 'data' => $qrCode], 200);

    }

 public function updateQrcode(Request $req)
    {
        $validator = Validator::make($req->all(), [
           'id' => 'required||exists:qr_code_generaters,id',
            'qrImage' => 'nullable|file|mimes:jpg,jpeg,png,webp',
            'logoImage' => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $tableColumns = Schema::getColumnListing('qr_code_generaters');

        $data = $req->all();

        $filteredData = array_filter($data, function($key) use ($tableColumns) {
            return in_array($key, $tableColumns) && $key !== 'logoImage' && $key !== 'qrImage';
        }, ARRAY_FILTER_USE_KEY);

        if ($req->hasFile('value') && $req->file('value')->isValid()) {
            $filteredData['value'] = $this->handleImageUpload($req->file('value'), 'qr_files');
        }
         if ($req->hasFile('qrImage') && $req->file('qrImage')->isValid()) {
            $filteredData['qrImage'] = $this->handleImageUpload($req->file('qrImage'), 'qr_images');
        }
        
        if (isset($data['logoImage'])) {
            $filteredData['logoImage'] = $this->handleImageUpload($data['logoImage'], 'qr_codes');
        }

            $qrCode = QrCodeGenerater::where('id',$req->id)->update($filteredData);
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
    public function getQrcode($isDownload = null){
        $userId = Auth::user()->id;
        if($isDownload == 1) {
            $qrcodes = QrCodeGenerater::where('user_id',$userId)->where('isDownload',1)->get();
        }
        else
        {
            $qrcodes = QrCodeGenerater::where('user_id',$userId)->get();
        }
        foreach ($qrcodes as $qrcode) {
            $qrcode->logoImage = isset($qrcode->logoImage) ? url($qrcode->logoImage) : '';
            $qrcode->qrImage = isset($qrcode->qrImage) ? url($qrcode->qrImage) : '';
        }
        return response()->json([
            'status' => true,
            'message' => "Success",
            'data' => $qrcodes,
        ]);
    }

}
