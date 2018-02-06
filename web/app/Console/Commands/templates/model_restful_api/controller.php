
<?php if ($namespace):?>
namespace App\Http\Controllers\<?=$namespace?>;
<?php else: ?>
namespace App\Http\Controllers;
<?php endif; ?>

use App\Http\Controllers\Controller;
use App\Services\Errors;
use App\Services\Result;
<?php if ($namespace):?>
use App\Services\<?=$namespace?>\<?=$modelName?>Service;
<?php else:?>
use App\Services\<?=$modelName?>Service;
<?php endif;?>
use Illuminate\Http\Request;


/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 18:06
 */
class <?=$modelName?>Controller extends Controller
{

    /**
     * @param Request $request
     * @return string
     *
     * @cat <?=$modelName . "\n"?>
     * @title 创建<?=$modelName?>对象
     * @comment 创建<?=$modelName?>对象
     *
     * @form-param some_data || string || 对象数据
     * @ret-val status
     */
    protected function create(Request $request)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }
        $result = <?=$modelName?>Service::create($data);

        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }

        $data = $result->data()->toArray();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     *
     * @cat <?=$modelName . "\n"?>
     * @title 编辑<?=$modelName?>对象
     * @comment 编辑<?=$modelName?>对象
     *
     * @form-param some_data || string || 对象数据
     * @ret-val status
     *
     * BUG: Laravel $request->input() can NOT read HTTP PUT payload ?!
     * @case id=1
     */
    protected function update(Request $request, $id)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }

        $result = <?=$modelName?>Service::update($id, $data);

        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }

        $data = $result->data()->toArray();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     *
     * @cat <?=$modelName . "\n"?>
     * @title 根据ID获取<?=$modelName?>对象数据
     * @comment 根据ID获取<?=$modelName?>对象数据
     * @url-param id || int || ID
     * @ret-val status
     * @ret-val create_time
     * @ret-val update_time
     *
     * @case id=1
     */
    protected function retrieve(Request $request, $id)
    {
        $result = <?=$modelName?>Service::retrieve($id);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }
        $data = $result->data();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @return string
     *
     * @cat <?=$modelName . "\n"?>
     * @title 获取<?=$modelName?>列表
     * @comment 获取<?=$modelName?>列表
     *
     * @ret-val list.0.status
     * @ret-val list.0.create_time
     * @ret-val list.0.update_time
     */
    protected function retrieveList(Request $request)
    {
        $filter = $request->input();

        $result = <?=$modelName?>Service::search($filter);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }
        $data = $result->data();
        return $this->json(Errors::Ok, $data);
    }

    // TODO: Remove

    /*__ACTION_PLACEHOLDER__*/

}