<?php

namespace App\Http\Controllers;
use App\Models\Approved;
use App\Models\Failure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use App\Models\Files;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{

    function getUsers()
    {
        return response(['status' => 'success', 'users' => User::all(), 'code' => 200]);
    }

    function upload(Request $request){

        ini_set('upload_max_filesize', '50M');
        ini_set('post_max_size', '50M');
        ini_set('max_input_time', 300);
        ini_set('max_execution_time', 300);

        $file = $request->file('file');
        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");
        //replace with your serve path to public
        $public = 'C:\OSPanel\domains\laravelvue\public\upload\/';

        File::append($path, $file->get());

        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name = basename($path, '.part');
            $namestore  = time().$name;
            File::move($path, $public.$namestore);
            $fileupload = new Files();
            $fileupload->user_id = $request->user_id;
            $fileupload->name = $name;
            $fileupload->file_name = $namestore;
            $fileupload->status = 0;
            $fileupload->save();
            return response()->json(['done' => true]);
        }
        return response()->json(['uploaded' => true]);
    }

    public function store(Request $request)
    {
        return response()->json(['uploaded' => true]);
    }
    function Status(Request $request){
        $files = Files::where('id',$request->id)->first();
        $files->status = 1;
        $files->save();
        return response(['status' => 'success', 'files' => $request->id, 'code' => 200]);
    }

	function Go(Request $request){

        $files = Files::where('id',$request->id)->first();
        //replace with your server artisan path
        $artisan = 'C:\OSPanel\domains\laravelvue\artisan';

        $process = Process::timeout(120)->start('php '.$artisan.' parse:csv '.$files->id);

        $result = $process->wait();

        return $result->output();
	}

    function getFiles(Request $request)
    {
        $files = Files::where('user_id',$request->id)->get();
        return response(['status' => 'success', 'files' => $files, 'code' => 200]);
    }

    function deleteFiles(Request $request){

        $file = Files::where('id',$request->id)->first();
        $file_pointer = public_path('upload')."/".$file->file_name;

        if (!unlink($file_pointer)) {
            return response(['status' => 'success','files' => $file,'code' => 500]);
        }

        Files::where('id',$request->id)->delete();
        Approved::where('file_id',$request->id)->delete();
        Failure::where('file_id',$request->id)->delete();

        return response(['status' => 'success','files' => $file,'code' => 200]);
    }

    function getApproved(Request $request){
        $data = Approved::where('file_id',$request->id)->orderBy('col_id','desc')->offset($request->page)->limit(10)->get();
        return response(['status' => 'success', 'approved' => $data, 'code' => 200]);
    }

    function getFailure(Request $request){
        $data = Failure::where('file_id',$request->id)->orderBy('col_id','desc')->offset($request->page)->limit(10)->get();
        return response(['status' => 'success', 'failure' => $data, 'code' => 200]);
    }
}
