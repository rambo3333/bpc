<?php

namespace App\Admin\Controllers;

use App\Models\Agent;
use App\Models\Franchisee;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Validation\Rule;

class WorkersController extends Controller
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

            $content->header('个代列表');

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

            $content->body(Admin::show(Worker::findOrFail($id), function (Show $show) {

                $show->id();
                $show->name('姓名');
                $show->mobile('手机号');
                $show->id_number_image_z('身份证正面')->image();
                $show->id_number_image_f('身份证反面')->image();
                $show->bank_image('银行卡')->image();
                $show->bank_name('银行名称');
                $show->bank_no('银行卡号');
                $show->bank('开户行');

                $show->created_at('创建时间');
                $show->updated_at('修改时间');
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

            $content->header('个代编辑');

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

            $content->header('个代添加');

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
        return Admin::grid(Worker::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('姓名');
            $grid->mobile('手机号');
            $grid->column('parent.name', '邀请人');

            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');

            $grid->disableExport();

            //查询
            $grid->filter(function($filter){
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('name', '姓名');
                $filter->equal('mobile', '手机号');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Worker::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '姓名')->rules('required');
            $form->text('mobile', '手机号')->rules(function($form){
                return [
                    'required',
                    Rule::unique('workers')->ignore($form->model()->id)
                ];
            });
            $form->image('id_number_image_z', '身份证正面')->uniqueName();
            $form->image('id_number_image_f', '身份证反面')->uniqueName();
            $form->text('bank_name', '银行名称')->default('');
            $form->text('bank_no', '银行卡号')->default('');
            $form->text('bank', '开户支行')->default('');
            $form->select('parent_id', '邀请人')->options(Worker::getSeletOptions());

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
