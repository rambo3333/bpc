<?php

namespace App\Admin\Controllers;

use App\Models\Agent;
use App\Models\Franchisee;
use App\Models\FranchiseeLevel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FranchiseesController extends Controller
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

            $content->header('加盟商列表');

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

            $content->body(Admin::show(Franchisee::findOrFail($id), function (Show $show) {

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

            $content->header('加盟商编辑');

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

            $content->header('加盟商创建');

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
        return Admin::grid(Franchisee::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('agent.name', '所属代理商');
            $grid->column('franchiseeLevel.star', '所属星级');
            $grid->franchisee_no('加盟商编号');
            $grid->name('店名');
            $grid->contact('联系人');
            $grid->telephone('手机号');
            $grid->address('地址');

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
        return Admin::form(Franchisee::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('franchisee_level_id', '所属星级')->options(FranchiseeLevel::getSeletOptions());
            $form->select('agent_id', '所属代理商')->options(Agent::getSeletOptions());
            $form->text('name', '店名');
            $form->text('contact', '联系人');
            $form->text('telephone', '手机号');
            $form->text('address', '地址');
            $form->saving(function (Form $form) {

                $form->model()->franchisee_no = 'jm' . request()->input('telephone');

            });

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
