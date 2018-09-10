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
            $form->html('', $label = '（全款）');

            $form->radio('one_dszzrx_status', '（第三者责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);
            $form->radio('one_dszzrx_default', '（第三者责任险）默认值')
                    ->options([1 => '50万', 2 => '100万'])
                    ->default(1);

            $form->text('one_clssx', '（车辆损失险）金额');
            $form->radio('one_clssx_status', '（车辆损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('one_qcdqx', '（全车盗抢险）金额');
            $form->radio('one_qcdqx_status', '（全车盗抢险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->text('one_blddpsx', '（玻璃单独破碎险）金额');
            $form->radio('one_blddpsx_status', '（玻璃单独破碎险）是否必选')
                ->options([0 => '否', 1 => '是'])
                ->default(0);

            $form->radio('one_csryzrx_status', '（车上人员责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);
            $form->radio('one_csryzrx_default', '（车上人员责任险）默认值')
                    ->options([1 => '1万/座', 2 => '2万/座', 3 => '5万/座'])
                    ->default(2);

            $form->text('one_bjmptyx', '（不计免赔特约险）金额');
            $form->radio('one_bjmptyx_status', '（不计免赔特约险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('one_wfzddsf', '（无法找到第三方）金额');
            $form->radio('one_wfzddsf_status', '（无法找到第三方）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('one_zrssx', '（自然损失险）金额');
            $form->radio('one_zrssx_status', '（自然损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->text('one_clsshuix', '（车辆涉水险）金额');
            $form->radio('one_clsshuix_status', '（车辆涉水险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);


            $form->divide();
            $form->html('', $label = '（贷款）');

            $form->radio('two_dszzrx_status', '（第三者责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);
            $form->radio('two_dszzrx_default', '（第三者责任险）默认值')
                    ->options([1 => '50万', 2 => '100万'])
                    ->default(1);

            $form->text('two_clssx', '（车辆损失险）金额');
            $form->radio('two_clssx_status', '（车辆损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('two_qcdqx', '（全车盗抢险）金额');
            $form->radio('two_qcdqx_status', '（全车盗抢险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('two_blddpsx', '（玻璃单独破碎险）金额');
            $form->radio('two_blddpsx_status', '（玻璃单独破碎险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->radio('two_csryzrx_status', '（车上人员责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);
            $form->radio('two_csryzrx_default', '（车上人员责任险）默认值')
                    ->options([1 => '1万/座', 2 => '2万/座', 3 => '5万/座'])
                    ->default(2);

            $form->text('two_bjmptyx', '（不计免赔特约险）金额');
            $form->radio('two_bjmptyx_status', '（不计免赔特约险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('two_wfzddsf', '（无法找到第三方）金额');
            $form->radio('two_wfzddsf_status', '（无法找到第三方）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('two_zrssx', '（自然损失险）金额');
            $form->radio('two_zrssx_status', '（自然损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->text('two_clsshuix', '（车辆涉水险）金额');
            $form->radio('two_clsshuix_status', '（车辆涉水险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->divide();
            $form->html('', $label = '（低首付/零首付）');

            $form->radio('three_dszzrx_status', '（第三者责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);
            $form->radio('three_dszzrx_default', '（第三者责任险）默认值')
                    ->options([1 => '50万', 2 => '100万'])
                    ->default(1);

            $form->text('three_clssx', '（车辆损失险）金额');
            $form->radio('three_clssx_status', '（车辆损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('three_qcdqx', '（全车盗抢险）金额');
            $form->radio('three_qcdqx_status', '（全车盗抢险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('three_blddpsx', '（玻璃单独破碎险）金额');
            $form->radio('three_blddpsx_status', '（玻璃单独破碎险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->radio('three_csryzrx_status', '（车上人员责任险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);
            $form->radio('three_csryzrx_default', '（车上人员责任险）默认值')
                    ->options([1 => '1万/座', 2 => '2万/座', 3 => '5万/座'])
                    ->default(2);

            $form->text('three_bjmptyx', '（不计免赔特约险）金额');
            $form->radio('three_bjmptyx_status', '（不计免赔特约险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('three_wfzddsf', '（无法找到第三方）金额');
            $form->radio('three_wfzddsf_status', '（无法找到第三方）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(1);

            $form->text('three_zrssx', '（自然损失险）金额');
            $form->radio('three_zrssx_status', '（自然损失险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->text('three_clsshuix', '（车辆涉水险）金额');
            $form->radio('three_clsshuix_status', '（车辆涉水险）是否必选')
                    ->options([0 => '否', 1 => '是'])
                    ->default(0);

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
