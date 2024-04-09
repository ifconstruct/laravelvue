<?php

namespace App\Console\Commands;
use App\Models\Approved;
use App\Models\Failure;
use Illuminate\Support\LazyCollection;
use App\Models\Files;
use Illuminate\Console\Command;

class ParseCSV extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'parse:csv';

    protected $signature = 'parse:csv {id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse CSV';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LazyCollection::make(function () {
            // project.csv  with 300.000 rows
            $id = $this->argument('id');
            $file = Files::where('id', $id)->first();
            $upload = public_path('upload');
            $file->status = 1;
            $file->save();
            $filePath = $upload."/".$file->file_name;
            $handle = fopen($filePath, 'r');
            while ($line = fgetcsv($handle)) {
                yield $line;
            }
        })
            ->chunk(900000) //max chunk querys
            ->each(function ($lines) {

                $count = 1;
                $errors = [];
                foreach ($lines as $line) {
                            $insert =[];
                            $fail = false;
                            $str_arr = explode (";", $line[0]);

                            $insert[] =[
                                'col1' => $str_arr[0],
                                'col2' => $str_arr[1],
                                'col3' => $str_arr[2],
                                'col4' => $str_arr[3],
                                'col5' => $str_arr[4],
                                'col6' => $str_arr[5],
                                'col7' => $str_arr[6],
                                'col8' => $str_arr[7],
                                'file_id' => (int) $this->argument('id'),
                                'col_id' => $count
                            ] ;

                            foreach($str_arr as $key => $val)
                            {
                                if(is_numeric($val) || empty($val) && !$fail ) {
                                    $fail =true ;
                                }
                            }

                            if($fail) {
                                Failure::insert($insert);
                            } else {
                                Approved::insert($insert);
                            }

                            $count++;

                }

                $id = $this->argument('id');
                $file = Files::where('id', $id)->first();
                $file->status = 2;
                $file->save();
            });

        echo json_encode(['status' => 'success','code' => 200]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [//			array('example', InputArgument::REQUIRED, 'An example argument.'),
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        ];
    }
}
