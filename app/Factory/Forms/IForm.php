<?php

namespace App\Factory\Forms;

interface IForm
{
    public const CSRF_PROTECTION_ERROR_MESSAGE = "Vypršel časový limit, odešlete formulář znovu";
    public const INPUT_SELECT_PROMPT = " - - - ";
}
