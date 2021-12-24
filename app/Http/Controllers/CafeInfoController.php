<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCafeInfo;
use http\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\CafeInfo;
use Illuminate\Http\Response;

class CafeInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $cafeInfo = CafeInfo::all();
            $result = [
                'data' => [
                    'message' => 'success',
                    'cafeInfo' => $cafeInfo
                ]
            ];
            return $this->resConversionJson($result);
        } catch(\Exception $e){
            $result = ['error' => ['messages' => [$e->getMessage()]]];
            return $this->resConversionJson($result, $e->getCode());
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
     * @return JsonResponse
     */
    public function store(StoreCafeInfo $request)
    {
        try {
            $cafeInfo = CafeInfo::create($request->all());
            $result = [
                'data' => [
                    'message' => 'success',
                    'cafeInfo' => $cafeInfo
                ]
            ];
            return $this->resConversionJson($result);
        } catch(Exception $e){
            $result = ['error' => ['messages' => [$e->getMessage()]]];
            return $this->resConversionJson($result, $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CafeInfo  $cafeInfo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = 'success';
        $statusCode = 200;
        try {
            $cafeInfo = CafeInfo::find($id);
            if (!$cafeInfo) {
                $message = 'cafe info not found';
                $statusCode = 404;
            }
            $result = [
                'data' => [
                    'message' => $message,
                    'cafeInfo' => $cafeInfo
                ]
            ];
            return $this->resConversionJson($result, $statusCode);
        } catch(Exception $e){
            $result = ['error' => ['messages' => [$e->getMessage()]]];
            return $this->resConversionJson($result, $e->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CafeInfo  $cafeInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(CafeInfo $cafeInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CafeInfo  $cafeInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CafeInfo $cafeInfo)
    {
        $message = 'success';
        $statusCode = 200;
        $update = [
            'outlet_count' => $request->outlet_count,
            'comment' => $request->comment
        ];
        try {
            $cafeInfo = CafeInfo::where('id', $id)->update($update);
            if (!$cafeInfo) {
                $message = 'cafe info not found';
                $statusCode = 404;
            }
            $result = [
                'data' => [
                    'message' => $message
                ]
            ];
            return $this->resConversionJson($result, $statusCode);
        } catch(Exception $e){
            $result = ['error' => ['messages' => [$e->getMessage()]]];
            return $this->resConversionJson($result, $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CafeInfo  $cafeInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(CafeInfo $cafeInfo)
    {
        $message = 'success';
        $statusCode = 200;
        try {
            $cafeInfo = CafeInfo::where('id', $id)->update(['delete_flag' => 1]);
            if (!$cafeInfo) {
                $message = 'cafe info not found';
                $statusCode = 404;
            }
            $result = [
                'data' => [
                    'message' => $message
                ]
            ];
            return $this->resConversionJson($result, $statusCode);
        } catch(Exception $e){
            $result = ['error' => ['messages' => [$e->getMessage()]]];
            return $this->resConversionJson($result, $e->getCode());
        }
    }
    
    //★
    private function resConversionJson($result, $statusCode = 200)
    {
        if(empty($statusCode) || $statusCode < 100 || $statusCode >= 600){
            $statusCode = 500;
        }
        return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_UNICODE);
    }
}
