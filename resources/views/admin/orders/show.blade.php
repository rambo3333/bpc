<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">订单流水号：{{ $order->order_no }}</h3>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>买家：</td>
                <td>@if ($order->user->name) {{$order->user->name}} - {{ $order->user->mobile }} @endif</td>
                <td>业务员：</td>
                <td>@if ($order->worker) {{$order->worker->name}} @endif</td>
            </tr>
            <tr>
                <td>支付时间：</td>
                <td>{{ $order->paid_at }}</td>
                <td>支付渠道单号：</td>
                <td>{{ $order->payment_no }}</td>
            </tr>
            <tr>
                <td>品牌车系车型：</td>
                <td>{{ $order->brand . ' ' . $order->series . ' ' . $order->cmodel }}</td>
                <td>指导价：</td>
                <td>{{ $order->guide_price }}</td>
            </tr>
            <tr>
                <td>优惠额度：</td>
                <td>{{ $order->pre_amount }}</td>
                <td>成交价：</td>
                <td>{{ $order->price }}</td>
            </tr>
            <tr>
                <td>图片：</td>
                <td colspan="3"><img src="/uploads/{{ $order->image }}" height="80px"></td>
            </tr>
            <tr>
                <td>购车方案：</td>
                <td>{{ $order->program }}</td>
            </tr>
            @if ($order->program != '全款')
                <tr>
                    <td>贷款方式：</td>
                    <td>{{ $order->dkfs }}</td>
                </tr>
                <tr>
                    <td>首付款：</td>
                    <td>{{ $order->sfk_text }}</td>
                </tr>
                <tr>
                    <td>贷款额：</td>
                    <td>{{ $order->dkje }}</td>
                </tr>
                <tr>
                    <td>GPS定位系统：</td>
                    <td>{{ $order->gps }}</td>
                </tr>
                <tr>
                    <td>抵押费：</td>
                    <td>{{ $order->dyf }}</td>
                </tr>
                <tr>
                    <td>手续费等杂费：</td>
                    <td>{{ $order->sxf }}</td>
                </tr>
                <tr>
                    <td>续保金：</td>
                    <td>{{ $order->xbj }}</td>
                </tr>
            @endif

            <tr>
                <td>购置税：</td>
                <td colspan="3">{{ $order->gzs }}</td>
            </tr>
            <tr>
                <td>上牌费：</td>
                <td colspan="3">{{ $order->spf }}</td>
            </tr>
            <tr>
                <td>车船税：</td>
                <td colspan="3">{{ $order->ccs }}</td>
            </tr>
            <tr>
                <td>交强险：</td>
                <td colspan="3">{{ $order->jqx }}</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #00c0ef">商业险：</td>
            </tr>
            <tr>
                <td>第三者责任险：</td>
                <td colspan="3">{{ $order->dszzrx_text }} （{{ $order->dszzrx }}）</td>
            </tr>
            <tr>
                <td>车辆损失险：</td>
                <td colspan="3">{{ $order->clssx }}</td>
            </tr>
            <tr>
                <td>全车盗抢险：</td>
                <td colspan="3">{{ $order->qcdqx }}</td>
            </tr>
            <tr>
                <td>玻璃单独破碎险：</td>
                <td colspan="3">{{ $order->blddpsx }}</td>
            </tr>
            <tr>
                <td>车上人员责任险（司机）：</td>
                <td colspan="3">{{ $order->sj_csryzrx_text }} （{{ $order->sj_csryzrx }}）</td>
            </tr>
            <tr>
                <td>车上人员责任险（乘客）：</td>
                <td colspan="3">{{ $order->ck_csryzrx_text }} （{{ $order->ck_csryzrx }}）</td>
            </tr>
            <tr>
                <td>不计免赔特约险：</td>
                <td colspan="3">{{ $order->bjmptyx }}</td>
            </tr>
            <tr>
                <td>无法找到第三方：</td>
                <td colspan="3">{{ $order->wfzddsf }}</td>
            </tr>
            <tr>
                <td>自燃损失险：</td>
                <td colspan="3">{{ $order->zrssx }}</td>
            </tr>
            <tr>
                <td>商业险合计：</td>
                <td colspan="3">{{ $order->syxhj }}</td>
            </tr>
            <tr>
                <td>商业险折扣：</td>
                <td colspan="3">{{ $order->syxzk / 10 }} 折</td>
            </tr>
            <tr>
                <td>折扣后金额：</td>
                <td colspan="3">{{ $order->zkhje }}</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #00c0ef">方案费用：</td>
            </tr>
            @if ($order->program == '全款')
            <tr>
                <td>成交总价：</td>
                <td colspan="3">{{ $order->cjzj }}</td>
            </tr>
            @else
            <tr>
                <td>首付款总额：</td>
                <td>{{ $order->sfk_total }}</td>
            </tr>
            <tr>
                <td>月供金额：</td>
                <td>{{ $order->ygje }}</td>
            </tr>
            <tr>
                <td>月供期数：</td>
                <td>{{ $order->ygqs }}</td>
            </tr>
            @endif
            <tr>
                <td>服务费：</td>
                <td colspan="3">{{ $order->fwf }}</td>
            </tr>
            <tr>
                <td>备注：</td>
                <td colspan="3">{{ $order->remark }}</td>
            </tr>
            <tr>
                <td>定金：</td>
                <td colspan="3">￥{{ $order->total_amount / 100 }}</td>
            </tr>
            <!-- 如果订单未确认，展示运费输入框 -->
            @if($order->closed === 0)
                <tr>
                    <td>
                        <form action="{{ route('admin.orders.confirm', [$order->id]) }}" method="post" class="form-inline">
                            <!-- 别忘了 csrf token 字段 -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group {{ $errors->has('yf') ? 'has-error' : '' }}">
                                <label for="yf" class="control-label">运费：</label>
                                <input type="text" id="yf" name="yf" value="" class="form-control" placeholder="输入运费">
                                @if($errors->has('yf'))
                                    @foreach($errors->get('yf') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success" id="ship-btn">确认订单</button>
                        </form>
                    </td>
                    <td colspan="3">
                        <form action="{{ route('admin.orders.refund', [$order->id]) }}" method="post" class="form-inline">
                            <!-- 别忘了 csrf token 字段 -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn btn-danger" id="ship-btn">退款</button>
                        </form>
                    </td>
                </tr>
            @else
                <tr>
                    <td>运费：</td>
                    <td colspan="3">{{ $order->yf }}</td>
                </tr>
                @if($order->refund_status === \App\Models\Order::REFUND_STATUS_SUCCESS)
                <tr>
                    <td>退款状态：</td>
                    <td colspan="3">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</td>
                </tr>
                @endif
            @endif
            </tbody>
        </table>
    </div>
</div>