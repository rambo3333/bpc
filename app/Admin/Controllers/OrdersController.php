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
use EasyWeChat\Factory;

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
            $grid->status('订单状态')->display(function($value) {
                return Order::$statusMap[$value];
            });
            $grid->closed('订单是否完成')->display(function($value) {
                return $value == 1 ? '是' : '否';
            });

            $grid->paid_at('支付时间')->sortable();
            $grid->created_at('下单时间');

            $grid->model()->orderBy('id', 'desc');
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
            // 将订单状态改为已确认
            $order->update([
                'yf' => $data['yf'],
                'closed'   => 1,
            ]);

            //剩业务员的操作

        });

        // 返回上一页
        return redirect()->back();
    }

    public function refund(Order $order, Request $request)
    {
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }

        // 判断订单退款状态是否正确
        if ($order->refund_status === Order::REFUND_STATUS_PROCESSING ||
            $order->refund_status === Order::REFUND_STATUS_SUCCESS) {
            throw new InvalidRequestException('该订单已经退款，请勿重复退款');
        }

        $refundNo = Order::getAvailableRefundNo();

        // 通过事务执行 sql
        \DB::transaction(function() use ($order, $refundNo) {
            // 将订单状态改为已确认（已签合同）
            $order->update([
                'refund_no' => $refundNo,
            ]);
        });

        $app = Factory::payment(config('wechat.payment.default'));

        // 参数分别为：微信订单号、商户退款单号、订单金额、退款金额、其他参数
        $result = $app->refund->byTransactionId($order->payment_no,
                                                $refundNo,
                                                $order->total_amount,
                                                $order->total_amount,
                                                []);

        if ($result['return_code'] === 'SUCCESS') {
            if ($result['result_code'] === 'SUCCESS') {
                // 退款成功，将订单退款状态改成退款成功
                $order->update([
                    'status' => Order::STATUS_REFUND,
                    'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    'closed' => 1,
                ]);

                return redirect()->back();
            } else {
                // 退款失败，将具体状态存入 extra 字段，并表退款状态改成失败
                $order->update([
                    'refund_status' => Order::REFUND_STATUS_FAILED,
                    'extra' => $result['err_code_des']
                ]);

                throw new InvalidRequestException($result['err_code_des']);
            }
        } else {
            throw new InvalidRequestException('通信失败，请稍后再通知我');
        }
    }
}
