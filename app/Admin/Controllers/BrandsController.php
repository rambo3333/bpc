<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Overtrue\Pinyin\Pinyin;

class BrandsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('品牌列表');

            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param $id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Detail');
            $content->description('description');

            $content->body(Admin::show(Brand::findOrFail($id), function (Show $show) {

                $show->id();

                $show->created_at();
                $show->updated_at();
            }));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑品牌');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('创建品牌');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Brand::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('品牌名称');
            $grid->name_py('名称拼音');
            $grid->image('品牌图片')->image();
            $grid->is_recommend('推荐')->display(function ($is_recommend) {
                return $is_recommend == 2 ? '是' : '否';
            });

            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Brand::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', '品牌名称');
            $form->image('image', '品牌图片')->uniqueName();

            $form->radio('is_recommend', '推荐')->options([1 => '否', 2=> '是'])->default(1);
            $form->number('sort', '排序');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $pinyin = new Pinyin(); // 默认
            $form->saving(function (Form $form) use ($pinyin) {

                $form->model()->name_py = substr($pinyin->abbr(request()->input('name')), 0, 1);

            });
        });
    }
}
