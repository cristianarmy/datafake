<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Faker;
use ArrayObject;

class DatasetController extends Controller
{
    public function index()
    {
        //recibimos un String DESDE FRONTEND  -----------
        /*
        status_list = ['Full Time', 'Part Time', 'Per Diem']
        team_list = fake.color(4)
        fake_workers = [{'x,
                  'Worker Name':fake.name(20), 
                  'Hire Date':fake.date(3),
                  'Worker Status':random(staus_list),
                  'Team':random(team_list)} for x in range(30)]
                  
        */
        $data_string = 
        "fake_workers = [{'x,
        'Worker Name' : fake .name(10),
        'Hire Date' : fake.date(5),
        'Production Date' : fake.date(),
        'Number Produced ' : fake.randomNumber(),
        'Worker Status' : random([part_time, full_time]),
        'Team' : random([azul, rojo, amarillo, verde]), 
        } for x in range(30)]";
        //----------------------------------------------
        //Limpiamos, retiramos rows y convertimos de string a array de atributos/fields 
        $split_array = $this->splitInputs($data_string);
        //$string = str_replace(" ", "",$string);
        $data_name = array_shift($split_array);
        $my_name = $this->splitTitle($data_name);
        //$dataset_name = $this->splitName($dataset_name);
        $data_row = array_pop($split_array);
        
        $fake_array = [];
        $no_fake_array =[];
        $only_fake_array =[];
        $first_type = "";    
        $partial_array = [];
        $faker = Faker\Factory::create('es_ES'); 
        foreach ($split_array as $split_string)
        {
            if(stristr($split_string, "fake") == TRUE) {

                if(stristr($split_string, "()") == TRUE)
                {
                    $llave = $this->splitName($split_string);
                    $valor =$this->splitType($split_string);
                    array_push($only_fake_array, [ $llave => $valor]);
                }
                else
                    {
                    if (empty($first_count))
                    $first_count = $this->splitCount($split_string);
                    array_push($fake_array, $this->factoryArray($split_string, $faker));
                    }
               
            }
            elseif(stristr($split_string, "random") == TRUE ) {
                array_push($no_fake_array, $this->splitArray($split_string));
            }
            
           
        }
        //var_dump($no_fake_array);
        
        //$faker->seed(10);
        
        for ($i=1; $i<=$first_count; $i++)
        {   
            
            $faker_row = [];
            //reset($fake_array);
            foreach ($fake_array as $fake_column)
            {
                $llave = array_key_first($fake_column);
                $valor = $fake_column[$llave];
                $faker_row[$llave] = array_rand(array_flip($valor), 1);
                    
                
            }
            foreach ($no_fake_array as $fake_column)
            {
                $llave = array_key_first($fake_column);
                $valor = $fake_column[$llave];
                $faker_row[$llave] = array_rand(array_flip($valor), 1);
                    
                
            }
           
            array_push($partial_array, $faker_row);
        
                  
              
        }
        $array_csv = [];
        $row = $this->splitCount($data_row);
        for ($i=0; $i<=($row-1); $i++)
        {
            shuffle($partial_array);
            foreach ($only_fake_array as $fake_column)
            {
                $llave = array_key_first($fake_column);
                $valor = $fake_column[$llave];
                
                //numberBetween(100, 500)
                $only[$llave] = $faker->$valor; 
                    
            
                $array_csv[$i] = array_merge($partial_array[1], $only);
            }
            
            
        }
        
        
        $this->createCsv($array_csv, $my_name);

        return view('vistafaker', compact('array_csv'));
        
    }

    private function splitInputs($string)
    {
        $split = "\n";
        $array_string = explode($split, $string);
        
        
        //var_dump($string);
        return $array_string;
    }

    private function createCsv($array, $name)
    {
        // Creamos y abrimos un archivo con el nombre 'archivo.csv' 
        //$name = $name.'.csv';
        $file = fopen($name.'.csv', 'w');
        // Escribimos los datos en el archivo 'archivo.csv'
        //$array = array_map("utf8_decode", $array);
        foreach ($array as $row )
        {
            fputcsv($file, $row);
        }
    
        // Después de terminar de escribir los datos, cerramos el archivo 'archivo.csv'
        fclose($file);
    } 

    private function splitCount($string)
    {
        $string = str_replace(" ", "",$string);

        
        $char = substr($string, (strlen($string)-1), 1);
        while  (is_numeric($char)==FALSE) 
        {
            
            $string = substr($string, 0, (strlen($string)-1));
            $char = substr($string, (strlen($string)-1), 1);
            

        }
        $count = "";
        while  (is_numeric($char)) 
        {
            $count = $char.$count ; 
            $string = substr($string, 0, (strlen($string)-1));
            $char = substr($string, (strlen($string)-1), 1);
            

        }
        //lo convertimos a entero
        $count = intval($count);
        return $count;
    }

    private function splitName($string)
    {
        //$string = str_replace(" ", "",$string);

        $name = "";
        $char = substr($string, 0, 1);
        
        while  ($char != ":") 
        {    
            
            $name = $name.$char ;        
            $string = substr($string, 1); 
            $char = substr($string, 0, 1);
        }
        $name = trim($name," '");        
        return $name;
    }

    private function splitTitle($string)
    {
        //$string = str_replace(" ", "",$string);

        $name = "";
        $char = substr($string, 0, 1);
        
        while  ($char != "=") 
        {    
            
            $name = $name.$char ;        
            $string = substr($string, 1); 
            $char = substr($string, 0, 1);
        }
        $name = trim($name," '");        
        return $name;
    }

    private function splitType($string)
    {
        $string = str_replace(" ", "",$string);

        $name = "";
        
        $char = substr($string, (strlen($string)-1), 1);
        while  ($char != ".") 
        {    
            $name = $char.$name ;        
            
            $string = substr($string, 0, (strlen($string)-1));
            $char = substr($string, (strlen($string)-1), 1);

        }
        $type = "";
        $char = substr($name, 0, 1);
       
        while  ($char != "(") 
        {    
            
            $type = $type.$char ;        
            $name = substr($name, 1); 
            $char = substr($name, 0, 1);
        }
               
        return $type;
    }

   private function factoryArray($string, $faker)
    {
        
        $type = $this->splitType($string);
        $llave = $this->splitName($string);

        $count = $this->splitCount($string);
        $array_f = [];
        for ($i=1; $i<=$count; $i++)
        {  
            array_push($array_f, $faker->$type); 
        }
        $array = [$llave => $array_f ]; 
        return $array;
    }
   
    private function splitArray($string)
    {
        

        $llave = $this->splitName($string);
        $string = str_replace(" ", "",$string);
        $array_i = explode('[', $string);
        $array_d = explode(']', $array_i[1]);
        $string_c = $array_d[0];

        $array_s = explode(',', $string_c);
        
        $array = [$llave => $array_s ];       

        return $array;
    }

}