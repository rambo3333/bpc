<?php

namespace App\Admin\Controllers;

use App\Models\Apply;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Extensions\Check;

class AppliesController extends Controller
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

            $content->header('个代审核');

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

            $content->header('个代详情');
            $content->description('description');

            $content->body(Admin::show(Apply::findOrFail($id), function (Show $show) {

                $show->id();

                $show->name('姓名');
                $show->mobile('手机号');
                $show->id_number_image_z('身份证正面')->image();
                $show->id_number_image_f('身份证反面')->image();
                $show->other_image('其他证明资料')->image();
                $show->bank_image('银行卡')->image();
                $show->bank('开户行');

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

            $content->header('Edit');
            $content->description('description');

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

            $content->header('Create');

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
        return Admin::grid(Apply::class, function (Grid $grid) {

            $grid->model()->orderBy('created_at', 'desc');
            $grid->id('ID')->sortable();

            $grid->name('申请人');
            $grid->mobile('手机号');
            $grid->column('worker.name', '邀请人');
            $grid->status('状态')->display(function ($status) {
                return $status == 1 ? '审核通过' : '待审核';
            });

            $grid->disableCreateButton();//禁止新建
            $grid->disableExport();//禁止导出

            $grid->actions(function ($actions) {
                // 添加操作
                $actions->prepend(new Check($actions->getKey()));
            });

            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Apply::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
