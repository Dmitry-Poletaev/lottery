<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use DateTime;
use DatePeriod;
use DateInterval;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $table = 'participants';

    public function getWinner($name)
    {
        
        
        //период лотереи
        $days = $this->intervalDate();
        //опреедляем текущее число
        $today = date("Y-m-d");

        if (in_array($today, $days)) {
            //проверяем есть ли победитель в этот день
            if ($this->existWinner($today) == false) {
                $this->getNumber($name, $today);
            } else {
                return redirect('/')->with('lose', 'К сожалению, Вы проиграли!');
            }
        } else {
            return redirect('/')->with('lose', 'К сожалению, лотерея уже закончилась!');
        }
        

        
    }

    public function addWinner($name, $day)
    {
        //добавляем победителя в бд
        $data = [];
        $data['day'] = $day;
        $data['winner_name'] = $name;
        DB::table('participants')->insert($data);
    }

    public function  intervalDate()
    {
        //даты начала и конца лотереи
        $begin = new DateTime('2021-01-02');
        $end = new DateTime('2021-01-09');
        $end = $end->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);
        //генериуем массив с датами
        $arr = [];
        foreach($daterange as $date){
            $date = $date->format("Y-m-d");
            array_push($arr, $date);
        }

        return $arr;
    }

    public function existWinner($day)
    {
        $date = DB::table('participants')->where('day', $day)->first();

        if (!empty($date)) {
            return true;
        } else {
            return false;
        }
    }

    public function getNumber($name, $day)
    {
        //генерируем случайное число
        $luckyNumber = rand(1, 100);

        //определяем победил или нет
        if ($luckyNumber % 3 == 0) {
            $this->addWinner($name, $day);
            return redirect('/')->with('win', 'Поздравляем, вы выиграли!');

        } else {
            return redirect('/')->with('lose', 'К сожалению, Вы проиграли!');
        }
    }
}