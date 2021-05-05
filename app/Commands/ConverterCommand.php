<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use SplFileObject;
use Flynsarmy\CsvSeeder\CsvSeeder;


class ConverterCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'conv {file= enter filename}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Convert to JSON";
    protected $file_type_default = ".csv";
    protected $column_headings_delimiter = "_";
    
    /**
     * Take CSV file name input and output JSON format
     * Run : php csv_json conv test
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file_name = strip_tags( $this->argument( 'file' ) );
        if (Str::contains($file_name, '.')) {
            $file = $file_name;
        } else {
            $file = $file_name . $this->file_type_default;
        }

        // $data = new SplFileObject( $file );
        // $file_type_actual = $data->getType();
        // print_r($file->extension());
        // print_r(filetype($file));
        if ( file_exists( $file ) ) {
            $this->info( $file . " Does Exist" );
            
            if (!($file_open = fopen($file, 'r'))) {
                die("Can't open file : " . $file);
            }
            
            $csv = array_map( 'str_getcsv', file( $file ));
            $headers = fgetcsv($file_open,"1024",",");
            $json = [];
            
            foreach ($csv as $i => $value) {
                if (!in_array($value[0], $headers) && $i>=1 ) {
                    $json[$i]["name"] = trim($value[0]);
                    if (isset($value[2])) {
                        $json[$i]['address'] = array("line1"=>trim($value[1]), "line2"=>trim($value[2]));
                    } else {
                        $json[$i]['address'] = array("line1"=>trim($value[1]));
                    }
                }
                
                // Arr::set($json[$i], 'name.address.line1', $value[1]);
            }
            
            print_r(json_encode($json, JSON_PRETTY_PRINT));//, JSON_FORCE_OBJECT
            
            fclose($file_open);
            
            /** if we had a DB */
            // User::create( ['name': $name] ..... );
        } else {
            $this->info( $file . " Not Exist");
        }
    }
    
    
    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
