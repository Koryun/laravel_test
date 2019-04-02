<?php

namespace App\Http\Controllers;

use App\Form;
use Illuminate\Http\Request;
use Storage;
use fillup\A2X;
use League\Flysystem\Filesystem;
use App\Providers\FileStorageProvider as FileStorage;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $files = Storage::disk('local')->files('forms/');
        $files = array_filter($files, function($file) { return (substr($file, -5) == '.json') !== 0; });
        foreach($files as $file) {
            $content = Storage::disk('local')->get($file);
            $content = json_decode($content, TRUE);
            $content['created_at'] = date("Y-m-d H:i:s", $content['created_at']);
            $content['id'] = substr($file, 0, -5);
            $data[] = $content;
        }
        
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = [
            'product_name' => $request->input('product_name'),
            'quantity' => $request->input('quantity'),
            'item_price' => $request->input('item_price')
        ];
        $validator = \Validator::make(
            $form,
            [
                'product_name'  => ['required'],
                'quantity'      => ['required', 'numeric'],
                'item_price'    => ['required', 'numeric']
            ]
        );
        
        if($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()->all()]);

        $form['created_at'] = time();
        $form['total_value_number'] = (int)$form['quantity'] * (float)$form['item_price'];
        $name = 'forms/'.time().str_random(10);
        Storage::disk('local')->put($name.'.json', json_encode($form));
        /*******************************************************/
        /*                       As XML                        */
        /*******************************************************/
        // $a2x = new A2X($form);
        // $xml = $a2x->asXml();
        // Storage::disk('local')->put($name.'.xml', $xml);
        return $this->index();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        return view('form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $form = [
            'id' => $request->input('id'),
            'product_name' => $request->input('product_name'),
            'quantity' => $request->input('quantity'),
            'item_price' => $request->input('item_price')
        ];
        $validator = \Validator::make(
            $form,
            [
                'id'            => ['required'],
                'product_name'  => ['required'],
                'quantity'      => ['required', 'numeric'],
                'item_price'    => ['required', 'numeric']
            ]
        );
        if($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()->all()]);
        if(!Storage::disk('local')->exists($form['id'].'.json'))
            return response()->json(['success' => false, 'errors' => ['File does not exist']]);

        $form['total_value_number'] = (int)$form['quantity'] * (float)$form['item_price'];
        
        $file = Storage::disk('local')->get($form['id'].'.json');
        $content = json_decode($file, TRUE);
        
        $form['created_at'] = $content['created_at'];
        $name = $form['id'];
        unset($form['id']);
        Storage::disk('local')->put($name.'.json', json_encode($form));

        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!empty($request->input('id'))) {
            if(Storage::disk('local')->exists($request->input('id').'.json')) {
                Storage::disk('local')->delete($request->input('id').'.json');
                return $this->index();
            }
            return response()->json(['success' => false, 'errors' => ['File does not exist']]);
        } 
    }
}
