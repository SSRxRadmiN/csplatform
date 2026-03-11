<?php

/**
 * Antispam Helper — вставляє захисні поля у форми
 *
 * Завантажувати:  helper('antispam')  або автозавантаження в BaseController
 */

if (! function_exists('antispam_fields')) {
    /**
     * Генерує HTML з honeypot + токеном часу.
     * Вставляти всередину <form> ... </form>
     *
     * @return string HTML-розмітка прихованих полів
     */
    function antispam_fields(): string
    {
        $token = \App\Filters\SpamFilter::generateToken();

        // Honeypot — виглядає як звичайне поле, але прихований через CSS.
        // Атрибут tabindex="-1" + autocomplete="off" — щоб юзер випадково не заповнив.
        // aria-hidden для screen readers.
        return <<<HTML
        <!-- antispam -->
        <div style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden;" aria-hidden="true">
            <label for="website">Залиште порожнім</label>
            <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="_formtoken" value="{$token}">
        HTML;
    }
}
