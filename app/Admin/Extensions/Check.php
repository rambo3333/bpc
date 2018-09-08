<?php
namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Check
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        $url = route('admin.api.check');
        return <<<SCRIPT

$('.grid-check-row').on('click', function () {

    // Your code.
    //alert($(this).data('id'));
    
    $.ajax({
        url: "$url",
        type: 'POST',
        data: JSON.stringify({   // 将请求变成 JSON 字符串
          id: $(this).data('id'),
          // 带上 CSRF Token
          // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
          _token: LA.token,
        }),
        contentType: 'application/json',  // 请求的数据格式为 JSON
        success: function (data) {  // 返回成功时会调用这个函数
          if (data.code == 0) {
            swal({
              title: data.msg,
              type: 'success'
            }, function() {
              // 用户点击 swal 上的 按钮时刷新页面
              location.reload();
            });
          } else {
            swal({
              title: data.msg,
              type: 'error'
            });
          }
        }
      });

});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='fa fa-check grid-check-row' title='审核通过' data-id='{$this->id}'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}