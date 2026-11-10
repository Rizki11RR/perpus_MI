<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\BackupLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BackupService
{
    public function create(int $userId): BackupLog
    {
        $name = 'sipus-'.$userId.'-'.now()->format('Ymd-His').'.json';
        $payload = json_encode(['created_at'=>now()->toIso8601String(),'tables'=>[
            'users'=>DB::table('users')->select(['id','name','username','email','role','is_active','created_at','updated_at'])->get(), 'book_categories'=>DB::table('book_categories')->get(), 'books'=>DB::table('books')->get(), 'book_copies'=>DB::table('book_copies')->get(), 'students'=>DB::table('students')->get(), 'teachers'=>DB::table('teachers')->get(), 'loans'=>DB::table('loans')->get(), 'loan_items'=>DB::table('loan_items')->get(),
        ]], JSON_THROW_ON_ERROR);
        Storage::disk('local')->put('backups/'.$name, $payload);
        $size = Storage::disk('local')->size('backups/'.$name);
        if ($size < 2) throw new RuntimeException('File backup tidak valid.');
        $log = BackupLog::create(['created_by'=>$userId,'file_name'=>$name,'path'=>'backups/'.$name,'file_size'=>$size,'type'=>'json','status'=>'verified','message'=>'Backup tersimpan dan diverifikasi.']);
        ActivityLog::create(['user_id'=>$userId,'action'=>'created','module'=>'backup','description'=>'Backup '.$name.' dibuat.']);
        return $log;
    }
}
