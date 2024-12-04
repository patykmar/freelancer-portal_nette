<?php

namespace App\Factory\Forms;

interface IForm
{
    public const string CSRF_PROTECTION_ERROR_MESSAGE = "Vypršel časový limit, odešlete formulář znovu";
    public const string INPUT_SELECT_PROMPT = " - - - ";
}
