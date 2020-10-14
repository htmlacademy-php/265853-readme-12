<?php

/**Набор функций для работы с датой*/
class TimeHelper
{
    /**
     * Получение относительного формата даты
     * @param $date DateTime дата которую нужно преобразовать
     *
     * @return string $timeHasPassed результат преобразования
     */
    function GetDateRelativeFormat(DateTime $date): string
    {
        $current_time = new DateTime('now');
        $interval = $date->diff($current_time);

        $minutes = $interval->format('%i');
        $hours = $interval->format('%H');
        $days = $interval->format('%d');
        $months = $interval->format('%m');
        $years = $interval->format('%Y');

        if ($years != 0) {
            $years = floor($years);
            $timeHasPassed = $years . ' ' . get_noun_plural_form($months, 'год', 'года', 'лет') . ' назад';
        } elseif ($months != 0) {
            $months = floor($months);
            $timeHasPassed = $months . ' ' . get_noun_plural_form($months, "месяц", "месяца", "месяцев") . " назад";
        } elseif ($days > 7 && $days < 35) {
            $week = floor($days / 7);
            $timeHasPassed = $week . ' ' . get_noun_plural_form($week, "неделя", "недели", "недель") . " назад";
        } elseif ($days != 0) {
            $timeHasPassed = $days . ' ' . get_noun_plural_form($days, "день", "дня", "дней") . " назад";
        } elseif ($hours != 0) {
            $hours = floor($hours);
            $timeHasPassed = $hours . ' ' . get_noun_plural_form($hours, "час", "часа", "часов") . " назад";
        } elseif ($minutes != 0) {
            $timeHasPassed = $minutes . ' ' . get_noun_plural_form($minutes, "минута", "минуты", "минут") . " назад";
        } else {
            $timeHasPassed = 'меньше минуты назад';
        }

        return $timeHasPassed;
    }

    /** Получить рандомную дату для поста
     * @param $index string элемент для которого нужна дата
     *
     * @return DateTime $random_date
     */
    function GetPostTime(string $index): DateTime
    {
        $random_date = generate_random_date($index);
        try {
            return new DateTime($random_date);
        } catch (Exception $e) {
        }
    }
}
