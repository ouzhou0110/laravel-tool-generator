<?php

namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class JokerPaginator extends LengthAwarePaginator
{
	/**
	 * 将新增的分页方法注册到查询构建器中，以便在模型实例上使用
	 * 注册方式：
	 * 在 AppServiceProvider 的 boot 方法中注册：AcademyPaginator::rejectIntoBuilder();
	 * 使用方式：
	 * 将之前代码中在模型实例上调用 paginate 方法改为调用 seoPaginate 方法即可：
	 * Article::where('status', 1)->seoPaginate(15, ['*'], 'page', page);
	 */
	public static function injectIntoBuilder()
	{
		Builder::macro('jokerPaginate', function ($perPage, $page) {
			$perPage = $perPage ?: $this->model->getPerPage();
			$items = ($total = $this->toBase()->getCountForPagination())
				? $this->forPage($page, $perPage)->get(['*'])
				: $this->model->newCollection();
			
			$data =  Container::getInstance()->makeWith(JokerPaginator::class, compact(
				'items', 'total', 'perPage', 'page'
			));
			$list = $data->getCollection(); // 保持数据为collection
			$data = $data->toArray();
			return [
				'pageSize' => $data['per_page'],
				'page' => $data['current_page'],
				'total' => $data['total'],
				'list' => $list,
			];
		});
	}
	
}