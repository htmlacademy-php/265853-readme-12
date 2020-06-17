<?php

/**Набор функций для работы с текстом*/
class StringHelper
{

    /**
     * Обрезает текст если количество символов больше чем заданное значение
     *
     * @param $text string Текст который нужно обработать
     * @param $number_char int Количество символов по умолчанию 300
     *
     * @return html вернет обработанные текст
     */
    function cropText(string $text, int $number_char = 300)
    {
        //разобьем текст на отдельные слова
        $split_text = explode(" ", $text);

        $word_length = 0;

        $reduction = false;
        $short_text[] = "";
        //считаем длину каждого слова
        foreach ($split_text as $word) {
            $word_length += mb_strlen($word, 'utf8') + 1;//использую mb_strlen т.к strlen выдает в 2 раза больше символов.
            if ($word_length >= $number_char) {
                $reduction = true;
                break;
            }
            $short_text[] = $word;
        };
        //обратно в текст
        $text = implode(" ", $short_text);

        if ($reduction != false) {
            return "<p>" . $text . "..." . "</p>" . '<a class="post-text__more-link" "href="#">Читать далее</a>';
        } else {
            return "<p>" . $text . "</p>";
        }
    }
}
