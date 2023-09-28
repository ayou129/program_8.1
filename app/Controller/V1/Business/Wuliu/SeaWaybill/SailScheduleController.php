<?php

declare(strict_types=1);
/**
 * @author liguoxin
 * @email guoxinlee129@gmail.com
 */

namespace App\Controller\V1\Business\Wuliu\SeaWaybill;

use App\Constant\ServiceCode;
use App\Controller\AbstractController;
use App\Model\WuliuSailSchedule;
use App\Model\WuliuSeaWaybill;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController]
class SailScheduleController extends AbstractController
{
    public function list()
    {
        $params = $this->getRequestAllFilter();
        $models = (new WuliuSailSchedule());
        $params['sort'] = $this->request->input('sort') ?? [];
        foreach ($params['sort'] as $item) {
            $sort = explode(',', $item);
            $sort_field = $sort[0];
            $sort_type = $sort[1];
            $models = $models->orderBy($sort_field, $sort_type);
        }

        $where = $whereOr = [];
        if (isset($params['created_at_start_time'])) {
            $where[] = [
                'created_at',
                '>=',
                $params['created_at_start_time'],
            ];
        }
        if (isset($params['created_at_end_time'])) {
            $where[] = [
                'created_at',
                '<=',
                $params['created_at_end_time'],
            ];
        }
        if (isset($params['name'])) {
            $where[] = [
                'name',
                'like',
                '%' . $params['name'] . '%',
            ];
        }
        if (isset($params['voyage'])) {
            $where[] = [
                'voyage',
                'like',
                '%' . $params['voyage'] . '%',
            ];
        }
        $models = $models->where($where)
            ->where(function ($query) use ($whereOr) {
                foreach ($whereOr as $item) {
                    $query->where(...$item[0])
                        ->orWhere(...$item[1]);
                }
            })
            ->with([
                'shipCompany',
            ]);

        $result = $models->paginate((int) $this->request->input('size', 10));
        $result = $result->toArray();
        return $this->responseJson(ServiceCode::SUCCESS, $result);
    }

    public function all() {}

    public function post()
    {
        $params = $this->getRequestAllFilter();
        # 关联数据

        # 检查是否重复
        $model = WuliuSailSchedule::where('name', $params['name'])
            ->where('voyage', $params['voyage'])
            ->first();
        if ($model) {
            throw new HttpException(ServiceCode::HTTP_CLIENT_PARAM_ERROR, '存在' . $params['name'] . $params['voyage'] . '的数据');
        }
        $model = new WuliuSailSchedule();
        // var_dump($params);
        $model->name = $params['name'];
        $model->voyage = $params['voyage'];
        $model->arrival_date = $params['arrival_date'];
        $model->ship_company_id = $params['ship_company_id'];
        $model->save();

        return $this->responseJson(ServiceCode::SUCCESS);
    }

    public function put()
    {
        $params = $this->getRequestAllFilter();
        // var_dump($params);
        // $params['dict_id'] = $params['dict']['id'];

        $model = WuliuSailSchedule::find($params['id']);
        if (! $model) {
            throw new HttpException(ServiceCode::HTTP_CLIENT_PARAM_ERROR);
        }

        $model->name = $params['name'];
        $model->voyage = $params['voyage'];
        $model->arrival_date = $params['arrival_date'];
        $model->ship_company_id = $params['ship_company_id'];
        $model->save();

        return $this->responseJson(ServiceCode::SUCCESS);
    }

    public function delete()
    {
        $params = $this->getRequestAllFilter();
        // var_dump($params);

        // 有海运单则不允许删除
        $sea = WuliuSeaWaybill::whereIn('sail_schedule_id', $params)->count();
        if ($sea) {
            throw new HttpException(ServiceCode::HTTP_CLIENT_PARAM_ERROR, '该船期下存在海运单，不可删除');
        }

        WuliuSailSchedule::whereIn('id', $params)
            ->delete();

        return $this->responseJson(ServiceCode::SUCCESS);
    }
}
