<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\Series;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Show;

class ApisController extends Controller
{
    use ModelForm;

    public function series(Request $request)
    {
        $brand_id = $request->get('q');
        $options = Series::where('brand_id', $brand_id)->select('id', 'name as text')->get();
        $selectOption = [];
        foreach ($options as $key => $option) {
            $selectOption[] = [
                'id' => $option->id,
                'text' => $option->text
            ];
        }
        return $selectOption;
    }
}
