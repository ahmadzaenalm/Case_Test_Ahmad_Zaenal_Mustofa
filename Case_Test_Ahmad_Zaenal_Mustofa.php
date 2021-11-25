<?php

class Sudoku{
    private $data;
    private $hasil;
    private $n;

    public function __construct($n,$data){
        $this->data=$data;
        $this->n=$n;
        $this->hasil=$data;
    }
    //menampilkan solusi
    public function print_value(){
       
        echo json_encode($this->hasil);
        //echo "<br>=====================================<br>";
        // for($i=0;$i<9;$i++){
        //     for($j=0;$j<9;$j++){
        //         echo "|".$data[$i][$j]."|";
        //     }
        //     echo "<br>";
        // }
       // echo "======================================<br>";
    }
    //mengecek apakah nilai valid pada kolom
    private function checkColumn($i,$j,$nilai,$hasil){
   
        $ada=true;
        $k=0;
        while($ada && $k<9){
            if($k!=$i){
                
                if($hasil[$k][$j]==$nilai){
                    $ada=false;
                }
            }
            $k++;
        }
        return $ada;
    }
    // mengecek apakah nilai valid pada baris
    private function checkRow($i,$j,$nilai,$hasil){
        $ada=true;
        $k=0;
        while($ada && $k<9){
            if($k!=$j){
                if($hasil[$i][$k]==$nilai){
                    $ada=false;
                }
            }
            $k++;
        }
        return $ada;
    }

    // mengecek apakah nilai valid pada kotak kecil 
    function checkBox($i,$j,$nilai,$hasil){
        $ada=true;
        $itmp=($i % $this->n);
        $iAtas=$i-$itmp;
        $iBawah=(($iAtas+$this->n)-1);
    
        $jtmp=($j%$this->n);
        $jAwal=$j-$jtmp;
        $jAkh=(($jAwal+$this->n)-1);
    
        $i=$iAtas;
        $j=$jAwal;
    
        while($ada && $i<=$iBawah){
            while($ada && $j<=$jAkh){
               
                if($hasil[$i][$j]==$nilai){
                  
                    $ada=false;
                }
                $j++;
            }
            $i++;
            $j=$jAwal;
        }
    
        return $ada;
    }
    function checkRule($i,$j,$nilai,$hasil){
        $column=$this->checkColumn($i,$j,$nilai,$hasil);
        $row=$this->checkRow($i,$j,$nilai,$hasil);
        $box=$this->checkBox($i,$j,$nilai,$hasil);
    
        if($column&&$row&&$box){
            //echo $nilai." memenuhi <br>";
            return true;
        }else{
            // echo $nilai." Tidak <br>";
            return false;
        }
    }
    
    function solusi(){
        $data=$this->data;
        $hasil=$this->hasil;
        $n=$this->n;
        $i=0;
        $j=0;
        $solusi=true;
        $nilai=1;
    
        while($i<($n*$n) && $i >=0 && $solusi){
            while($j < ($n*$n) && $solusi){
                if($hasil[$i][$j]==0){
                    //echo "mau cek angka".$nilai."index".$i."|".$j."<br>";
                    while(!$this->checkRule($i,$j,$nilai,$hasil) && $nilai<=($n*$n)){
                       
                        $nilai++;
                       //echo "cek angka".$nilai."index".$i."|".$j."<br>";
                    }
                    if($nilai>($n*$n)){
                        //echo "angka tidak ada yg cocok untuk index".$i."|".$j."<br>";
                        //print_value($hasil);
                        do{
                            $j--;
                            if($j<0){
                                $j+=($n*$n);
                                $i--;
                                if($i<0){
                                    $solusi=false;
                                    $i++;
                                }
                            }
                            //echo "index-2".$i."|".$j."<br>";
                            if($data[$i][$j]==0){
                                $nilai=$hasil[$i][$j];
                                $hasil[$i][$j]=0;
                               // echo "set nol<br>";
                            }
                        }while($data[$i][$j]!=0);
                        
                        //echo "ambil nilai ".$nilai."di index".$i."|".$j."<br>";
                        $nilai++;
                    }else{
                        $hasil[$i][$j]=$nilai;
                        $j++;
                        $nilai=1;
                    }
                   
                }else{
                   
                    $j++;
                }
                
            }
            $i++;
            $j=0;
        }
        
        if($solusi){
           $this->hasil=$hasil;
            return true;
        }else{
            return false;
        }
       
    }

}
if(isset($_GET['function'])){
    if(function_exists($_GET['function'])) {
        $_GET['function']();
    }else{
        echo "Nama Fungsi tidak ditemukan";
    }
}else{
    echo "Fungsi belum ditentukan";
}
function get_solusi(){
    //cegah akses method get
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        echo "Maaf Method GET tidak diperbolehkan";
        exit();
    }
    // inputan test case
    // $data=[
    //     [0,2,3,4,0,0,7,8,9],
    //     [4,0,7,0,8,0,0,0,0],
    //     [8,6,9,1,0,0,0,4,0],
    //     [2,0,0,0,0,0,0,0,0],
    //     [0,9,6,0,0,0,0,0,7],
    //     [0,4,0,9,0,1,5,0,0],
    //     [0,0,4,3,0,0,6,0,0],
    //     [0,0,0,0,4,5,0,0,3],
    //     [9,0,2,0,0,0,8,0,0],
    // ];
    //ambil data yg dikirim
    $data=json_decode(file_get_contents('php://input'),TRUE);
    //membuat obj Sudoku dg inputan pertama jumlah n*n bidang dan inputan ke-2 berupa data array 2d inputan case sudoku
    $sudo=new Sudoku(3,$data);
    // panggil fungsi solusi
    $hasil=$sudo->solusi();
    //cek hasil
    if($hasil){
        $sudo->print_value();
    }else{
        echo "Tidak ada solusi";
    }
   
}



?>