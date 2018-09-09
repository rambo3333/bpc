<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Models\Series;
use App\Models\Cmodel;
use App\Models\Stype;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CmodelsController extends Controller
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

            $content->header('车型列表');

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

            $content->body(Admin::show(Cmodel::findOrFail($id), function (Show $show) {

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

            $content->header('车型编辑');

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

            $content->header('车型创建');

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
        return Admin::grid(Cmodel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('brand.name', '所属品牌');
            $grid->column('series.name', '所属车系');
            $grid->name('车型名称');
            $grid->image('车型图片')->image();
            $grid->guide_price('指导价');
            $grid->pre_amount('优惠额度');
            $grid->price('成交价');
            $grid->column('stype.name', '所属类型');

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
        return Admin::form(Cmodel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('brand_id', '所属品牌')->options(Brand::getSeletOptions())->load('series_id', '/admin/api/series');
            $form->select('series_id', '所属车系')->options(function($id) {
                return Series::where('id', $id)->pluck('name', 'id');
            });
            $form->text('name', '车系名称');
            $form->image('image', '图片')->uniqueName();
            $form->text('guide_price', '指导价');
            $form->text('pre_amount', '优惠额度');
            $form->text('pl', '排量');
            $form->text('zw', '座位数');
            $form->select('stype_id', '所属类型')->options(Stype::getSeletOptions());

            $form->divide();

            $form->radio('one_dszzrx_status', '（全款）第三者责任险是否必选')->options([0 => '否', 1=> '是'])->default(0);
            $form->text('one_clssx', '（全款）车辆损失险');
            $form->radio('one_clssx_status', '（全款）车辆损失险是否必选')->options([0 => '否', 1=> '是'])->default(0);
            $form->text('one_qcdqx', '（全款）全车盗抢险');
            $form->radio('one_qcdqx_status', '（全款）全车盗抢险是否必选')->options([0 => '否', 1=> '是'])->default(1);



            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
