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
                <td>@if ($order->user->name) {{$order->user->name}} @endif</td>
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
                <td>备注：</td>
                <td colspan="3">{{ $order->remark }}</td>
            </tr>
            {{--<tr>
                <td>收货地址</td>
                <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
            </tr>
            <tr>
                <td rowspan="{{ $order->items->count() + 1 }}">商品列表</td>
                <td>商品名称</td>
                <td>单价</td>
                <td>数量</td>
            </tr>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
                    <td>￥{{ $item->price }}</td>
                    <td>{{ $item->amount }}</td>
                </tr>
            @endforeach--}}
            <tr>
                <td>定金：</td>
                <td colspan="3">￥{{ $order->total_amount }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>