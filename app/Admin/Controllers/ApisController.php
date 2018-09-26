<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\Series;
use App\Models\Apply;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Show;
use DB;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Log;

class ApisController extends Controller
{
    use ModelForm;

    public function series(Request $request)
    {
        $brand_id = $request->get('q');
        $options = Series::where('brand_id', $brand_id)->select('id', 'name as text')->get();
        $selectOption = [];
        foreach ($options as $key => $option) {
            $selectOption[] = [
                'id' => $option->id,
                'text' => $option->text
            ];
        }
        return $selectOption;
    }

    //个代审核
    public function check(Request $request, EasySms $easySms)
    {
        $id = $request->get('id');

        $apply = Apply::find($id);

        if ($apply->status == 1) {
            return ['code' => 1, 'msg' => '请勿重复审核'];
        }

        $transaction_flag = true;
        DB::beginTransaction();
        //审核表，修改状态，审核通过
        $apply->status = 1;
        $apply_flag = $apply->save();
        if (!$apply_flag) {
            $transaction_flag = false;
        }
        //业务员表添加一条数据
        $worker = new Worker;
        $worker->agent_id = $apply->agent_id;
        $worker->franchisee_id = $apply->franchisee_id;
        $worker->password = $apply->password;
        $worker->name = $apply->name;
        $worker->mobile = $apply->mobile;
        $worker->worker_no = $apply->mobile;
        $worker->id_number_image_z = $apply->id_number_image_z;
        $worker->id_number_image_f = $apply->id_number_image_f;
        $worker->other_image = $apply->other_image;
        $worker->bank_image = $apply->bank_image;
        $worker->bank = $apply->bank;
        $worker->parent_id = $apply->worker_id;
        $worker->user_id = $apply->user_id;
        $worker_flag = $worker->save();
        if (!$worker_flag) {
            $transaction_flag = false;
        }

        if ($transaction_flag) {
            DB::commit();

            //发送审核通过的短信
            try {
                $result = $easySms->send($apply->mobile, [
                    'content'  =>  "【便便车】尊敬的用户，您的帐号{$worker->mobile}成功通过平台审核，如有疑问请联系客服。"
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                $msg = $message ?? '短信发送异常';
                Log::info('个代审核短信' . $msg);
            }

            return ['code' => 0, 'msg' => '操作成功'];
        } else {
            DB::rollBack();
            return ['code' => 1, 'msg' => '操作失败'];
        }
    }
}
