<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;

class OrdersController extends Controller
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

            $content->header('Index');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param $id
     * @return Content
     */
    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {

            $content->header('Detail');
            $content->description('description');

            $content->body(view('admin.orders.show', ['order' => $order]));
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
            $content->description('description');

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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->order_no('平台单号');
            $grid->column('user.name', '昵称');
            $grid->column('worker.name', '业务员');
            $grid->program('购车方案');
            $grid->status('退款状态')->display(function($value) {
                return Order::$statusMap[$value];
            });

            $grid->paid_at('支付时间')->sortable();
            $grid->created_at('下单时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function confirm(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }
        // 判断当前订单发货状态是否为未发货
        if ($order->contract === 1) {
            throw new InvalidRequestException('该订单已确认过');
        }
        // Laravel 5.5 之后 validate 方法可以返回校验过的值
        $data = $this->validate($request, [
            'yf' => ['required'],
        ], [], [
            'yf' => '运费',
        ]);

        // 通过事务执行 sql
        \DB::transaction(function() use ($order, $data) {
            // 将订单状态改为已确认（已签合同）
            $order->update([
                'yf' => $data['yf'],
                'contract'   => 1,
            ]);

            //剩业务员的操作

        });

        // 返回上一页
        return redirect()->back();
    }

    public function refund(Order $order, Request $request)
    {
        /*$refundNo = Order::getAvailableRefundNo();
        app('wechat_pay')->refund([
            'out_trade_no' => $order->no, // 之前的订单流水号
            'total_fee' => $order->total_amount * 100, //原订单金额，单位分
            'refund_fee' => $order->total_amount * 100, // 要退款的订单金额，单位分
            'out_refund_no' => $refundNo, // 退款订单号
            // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
            'notify_url' => 'http://requestbin.fullcontact.com/******' // 由于是开发环境，需要配成 requestbin 地址
        ]);
        // 将订单状态改成退款中
        $order->update([
            'refund_no' => $refundNo,
            'refund_status' => Order::REFUND_STATUS_PROCESSING,
        ]);*/
    }
}
