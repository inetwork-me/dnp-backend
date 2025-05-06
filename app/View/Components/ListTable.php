<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListTable extends Component
{
    public $tableHeaders, $tableKeys, $tableData, $title, $addRoute, $showActions, $showRoute, $editRoute, $deleteRoute;

    public function __construct($tableHeaders, $tableKeys, $tableData, $title = null, $addRoute = null, $showActions = true, $showRoute = null, $editRoute = null, $deleteRoute = null)
    {
        $this->tableHeaders = $tableHeaders;
        $this->tableKeys = $tableKeys;
        $this->tableData = $tableData;
        $this->title = $title;
        $this->addRoute = $addRoute;
        $this->showActions = $showActions;
        $this->showRoute = $showRoute;
        $this->editRoute = $editRoute;
        $this->deleteRoute = $deleteRoute;
    }
    
    public function render(): View|Closure|string
    {
        return view('components.list-table');
    }
}
