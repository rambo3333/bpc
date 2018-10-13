<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\Worker;
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

            //业务员的操作
            if ($order->worker_no) {
                /**
                 * 对当前业务员的影响
                 */
                //获取业务员信息
                $worker = Worker::where('worker_no', '=', $order->worker_no)->first();

                //查看当前业务员是否有效，如果无效，改成有效。当前考核时间销售数量加1，销售总数量加1
                if ($worker->is_effect == 0) {
                    $worker->update([
                        'is_effect' => 1,
                        'sale_num' => $worker->sale_num + 1,
                        'sale_total_num' => $worker->sale_total_num + 1,
                    ]);
                } else {
                    //当前考核时间销售数量加1，销售总数量加1
                    $worker->update([
                        'sale_num' => $worker->sale_num + 1,
                        'sale_total_num' => $worker->sale_total_num + 1,
                    ]);
                }

                //判断是否符合升级条件（满足3个条件：1.星星数量满足当前级别、2.当前级别不是最高级别、3.销售数量达到下一级别的目标）
                if (($worker->star == $worker->getStarMax($worker->level)) &&
                    ($worker->level < 4) &&
                    ($worker->sale_num >= $worker->getNextLevelSaleGoalNum($worker->level))) {
                    $worker->update(['level' => $worker->level + 1, 'upgraded_at' => date('Y-m-d H:i:s')]);

                    //记录佣金明细及月度总计
                    $this->recordCommission($worker, $order);
                } else {
                    //记录佣金明细及月度总计
                    $this->recordCommission($worker, $order);
                }

                /**
                 * 对上级们的影响
                 */
                //判断是否存在上级
                if ($worker->parent_id) {
                    //判断上级是否符合升级要求，符合就进行升级
                    $worker_parent_one = $this->upgrade($worker->parent_id);
                    //与该业务员存在一级关系，只有支队长才能获得收益
                    if ($worker_parent_one->manage_level >= 1) {
                        $this->recordCommission($worker_parent_one, $order, 1);
                    }

                    //判断是否存在上上级，如果存在，就判断是否符合升级要求，符合就进行升级
                    if ($worker_parent_one->parent_id) {
                        $worker_parent_two = $this->upgrade($worker_parent_one->parent_id);
                        //与该业务员存在二级关系，只有大队长才能获得收益
                        if ($worker_parent_two->manage_level >= 2 &&
                            ($worker_parent_one->manage_level == ($worker_parent_two->manage_level - 1))) {
                            $this->recordCommission($worker_parent_two, $order, 2);
                        }

                        //判断是否存在上上上级，如果存在，就判断是否符合升级要求，符合就进行升级
                        if ($worker_parent_two->parent_id) {
                            $worker_parent_three = $this->upgrade($worker_parent_two->parent_id);
                            //与该业务员存在三级关系，只有总队长才能获得收益
                            if ($worker_parent_three->manage_level == 3 && ($worker_parent_two->manage_level == 2)) {
                                $this->recordCommission($worker_parent_three, $order, 3);
                            }
                        }
                    }
                }
            }
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

    //记录佣金明细及月度总计
    protected function recordCommission(Worker $worker, Order $order, $relation_level = 0)
    {
        //判断收益类型
        if ($worker->id != $order->worker->id) {
            $type = 2; //管理收益
            if ($relation_level == 1) {
                $commission_rate = config('car.relation_level1');
            }
            if ($relation_level == 2) {
                $commission_rate = config('car.relation_level2');
            }
            if ($relation_level == 3) {
                $commission_rate = config('car.relation_level3');
            }
        } else {
            $type = 1; //销售收益
            $commission_rate = $worker->getCommissionRate($worker->level);
        }
        $commission = $order->fwf * $commission_rate;
        /**
         * 记录佣金明细
         */
        $commission_detail_data = [
            'type' => $type,
            'worker_id' => $worker->id,
            'third_id' => $order->worker->id,
            'order_no' => $order->order_no,
            'user_id' => $order->user_id,
            'level_name' => $worker->getLevelName($worker->level),
            'level' => $worker->level,
            'commission_rate' => $commission_rate * 100 . '%',
            'commission' => $commission,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        \DB::table('commission_detail')->insert($commission_detail_data);

        /**
         * 记录佣金月度表
         */
        //判断本月是否有数据
        $commission_month_where = [
            'year' => date('Y'),
            'month' => date('m'),
            'worker_id' => $worker->id,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $commission_month = \DB::table('commission_month')->where($commission_month_where)->first();

        $commission_month_data = [
            'year' => date('Y'),
            'month' => date('m'),
            'worker_id' => $worker->id,
        ];
        if (!empty($commission_month)) {
            $commission += $commission_month->commission;
            $commission_month_data['commission'] = $commission;
            \DB::table('commission_month')->where('id', '=', $commission_month->id)->update($commission_month_data);
        } else {
            $commission_month_data['commission'] = $commission;
            \DB::table('commission_month')->insert($commission_month_data);
        }
    }

    //判断业务员是否符合管理升级要求，符合则进行升级
    protected function upgrade($worker_id)
    {
        //获取业务员信息
        $worker = Worker::find($worker_id);

        switch ($worker->manage_level) {
            case 0:
                //查看直属个代数量是否满足升级要求
                $underling_count = Worker::where(['parent_id' => $worker->id, 'is_effect' => 1])->count();
                if ($underling_count == config('car.manage_level1')) {
                    $worker->update(['manage_level' => 1]);
                }
                return $worker;
            case 1:
                //查看直属支队长是否满足升级要求
                $underling_count = Worker::where(['parent_id' => $worker->id, 'manage_level' => 1])->count();
                if ($underling_count == config('car.manage_level2')) {
                    $worker->update(['manage_level' => 2]);
                }
                return $worker;
            case 2:
                //查看直属大队长是否满足升级要求
                $underling_count = Worker::where(['parent_id' => $worker->id, 'manage_level' => 2])->count();
                if ($underling_count == config('car.manage_level3')) {
                    $worker->update(['manage_level' => 3]);
                }
                return $worker;
            default:
                return $worker;
        }
    }
}
