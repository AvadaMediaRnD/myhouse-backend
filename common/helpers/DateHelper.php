<?php

namespace common\helpers;

class DateHelper {
    
    /**
     * Get month + year text label
     * @param string $inDate input date value
     * @param boolean $ua is months in ua/ru
     * @return string
     */
    public static function getMonthYearLabel($inDate, $ua = false)
    {
        $months = [
            '',
            'Январь',
            'Февраль',
            'Март',
            'Апрель',
            'Май',
            'Июнь',
            'Июль',
            'Август',
            'Сентябрь',
            'Октябрь',
            'Ноябрь',
            'Декабрь'
        ];
        $monthsUa = [
            '',
            'Січень',
            'Лютий',
            'Березень',
            'Квітень',
            'Травень',
            'Червень',
            'Липень',
            'Серпень',
            'Вересень',
            'Жовтень',
            'Листопад',
            'Грудень'
        ];
        
        $ts = strtotime($inDate);
        $monthNum = date('n', $ts);
        $yearNum = date('Y', $ts);
        
        return ($ua ? $monthsUa[$monthNum] : $months[$monthNum]) . ' ' . $yearNum;
    }   
}
