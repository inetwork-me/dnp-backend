<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableCard extends Component
{
    public $title;
    public $link;

    public function __construct($title,$link)
    {
        $this->title = $title;
        $this->link = $link;
    }

    public function render(): View|Closure|string
    {
        return view('components.table-card');
    }
}
